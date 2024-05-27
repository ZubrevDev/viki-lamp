<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Model_Service_Shipment')) :

/**
 * This service offers functions to manage shipments
 */
class DHLPWC_Model_Service_Shipment extends DHLPWC_Model_Core_Singleton_Abstract
{
    const CREATE_ERROR = 'create';

    protected $errors = array();

    /**
     * Create a shipment with label data attached to order_id. Optionally, request specific sizes
     *
     * @param $order_id
     * @param null $label_size
     * @return boolean
     */
    public function create($order_id, $pieces, $shipment_options = array(), $shipment_option_data = array(), $to_business = false)
    {
        $this->clear_error(self::CREATE_ERROR);
        $logic = DHLPWC_Model_Logic_Shipment::instance();

        // Return label logic
        $return_option = $logic->check_return_option($shipment_options);
        if ($return_option) {
            $shipment_options = $logic->remove_return_option($shipment_options);
        }

        // Hide sender label logic
        $hide_sender_data = $logic->get_hide_sender_data($shipment_option_data);
        if ($hide_sender_data) {
            $shipment_option_data = $logic->remove_hide_sender_data($shipment_option_data);
        }

        /** @var DHLPWC_Model_API_Data_Shipment_Request $shipment_data */
        $shipment_data = $logic->prepare_data($order_id, array(
            'pieces' => $pieces,
            'label_options' => $shipment_options,
            'label_option_data' => $shipment_option_data,
            'to_business' => $to_business,
        ), $hide_sender_data);

        // Get validation rules
        $access_service = DHLPWC_Model_Service_Access_Control::instance();
        $validate_address_number = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_VALIDATION_RULE, 'address_number');

        // Cancel request if no street and housenumber are set
        if (empty($shipment_data->shipper->address->street)) {
            $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Shipper %s field is required.', 'dhlpwc'), __('street', 'dhlpwc'))));
            return false;
        }
        if (empty($shipment_data->shipper->address->number) && $validate_address_number) {
            $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Shipper %s field is required.', 'dhlpwc'), __('house number', 'dhlpwc'))));
            return false;
        }

        if (empty($shipment_data->receiver->address->street)) {
            $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Receiver %s field is required.', 'dhlpwc'), __('street', 'dhlpwc'))));
            return false;
        }
        if (empty($shipment_data->receiver->address->number) && $validate_address_number) {
            $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Receiver %s field is required.', 'dhlpwc'), __('house number', 'dhlpwc'))));
            return false;
        }

        // Validate if using hide_sender_data
        if ($hide_sender_data) {
            if ((empty($shipment_data->on_behalf_of->name->first_name) || empty($shipment_data->on_behalf_of->name->last_name)) && empty($shipment_data->on_behalf_of->name->company_name)) {
                $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Hide shipper %s field is required.', 'dhlpwc'), __('company', 'dhlpwc'))));
                return false;
            }
            if (empty($shipment_data->on_behalf_of->address->street)) {
                $this->set_error(self::CREATE_ERROR, ucfirst(printf(__('Hide shipper %s field is required.', 'dhlpwc'), __('street', 'dhlpwc'))));
                return false;
            }
            if (empty($shipment_data->on_behalf_of->address->city)) {
                $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Hide shipper %s field is required.', 'dhlpwc'), __('city', 'dhlpwc'))));
                return false;
            }
            if (empty($shipment_data->on_behalf_of->address->number) && $validate_address_number) {
                $this->set_error(self::CREATE_ERROR, ucfirst(sprintf(__('Hide shipper %s field is required.', 'dhlpwc'), __('house number', 'dhlpwc'))));
                return false;
            }
        }

        $response = $logic->send_request($shipment_data);
        if (!$response || empty($response['pieces'])) {
            return false;
        }


        $created_label_ids = $this->save_labels($response, $shipment_data, $order_id);

        /** Create return label if requested */
        if ($return_option) {
            $return_shipment_data = $logic->get_return_data($shipment_data);

            $return_response = $logic->send_request($return_shipment_data);
            if (!$return_response || empty($response['pieces'])) {
                // This failed. Remove original successfully created label as well and return.
                foreach ($created_label_ids as $label_id) {
                    $this->delete($order_id, $label_id);
                }

                return false;
            }

            $this->save_labels($return_response, $return_shipment_data, $order_id, true);
        }

        $this->update_order_status($order_id);

        return true;
    }

    protected function save_labels($response, $shipment_data, $order_id, $is_return = false)
    {
        $labels = $response['pieces'];
        // We keep track of the label ids, used to remove these labels if creating a return label fails.
        $created_label_ids = [];
        foreach ($labels as $label) {
            $created_label_ids[] = $label['labelId'];
            $label_data = array(
                'label_id' => $label['labelId'],
                'label_type' => $label['labelType'],
                'label_size' => $label['parcelType'],
                'tracker_code' => $label['trackerCode'],
                'routing_code' => null,
                'order_reference' => $response['orderReference'],
                'is_return' => $is_return
            );

            // Save label request or not
            $service = DHLPWC_Model_Service_Access_Control::instance();
            $debug_label_requests = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_LABEL_REQUEST);
            if ($debug_label_requests) {
                $label_data['request'] = json_encode($shipment_data);
            }

            // Create label do action hook
            do_action('dhlpwc_create_label', $order_id, $label_data);

            $meta = new DHLPWC_Model_Service_Order_Meta();
            $meta->save_label($order_id, $label_data);
        }

        return $created_label_ids;
    }

    public function bulk($order_ids, $bulk_size, $bulk_service_options = array())
    {
        $bulk_success = 0;
        $bulk_fail = 0;

        foreach ($order_ids as $order_id) {
            $selected_size = $bulk_size;

            // Generate to business option
            $access_service = DHLPWC_Model_Service_Access_Control::instance();
            $to_business = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DEFAULT_TO_BUSINESS);

            // Generate options
            $option_service = DHLPWC_Model_Service_Order_Meta_Option::instance();
            $preset_options = $option_service->get_keys($order_id);

            // Add selected bulk service options
            if (!empty($bulk_service_options)) {
                $preset_options = array_merge($preset_options, $bulk_service_options);
            }

            if ($option_service->send_with_bp($order_id)) {
                // Simulate bp_only print when eligible for BP
                $service = DHLPWC_Model_Service_Access_Control::instance();
                $sizes = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_CAPABILITY_PARCELTYPE, array(
                    'order_id' => $order_id,
                    'options' => [DHLPWC_Model_Meta_Order_Option_Preference::OPTION_BP],
                    'to_business' => $to_business,
                ));

                if (!empty($sizes)) {
                    $selected_size = 'bp_only';
                }
            }

            // Only apply special delivery method logic if it's not an PS.
            if (!in_array(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PS, $preset_options)) {
                // BP preference
                if ($selected_size === 'bp_only' || $selected_size === 'envelope_only') {
                    if ( in_array(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_DOOR, $preset_options)) {
                        // Remove DOOR
                        $preset_options = array_diff($preset_options, [DHLPWC_Model_Meta_Order_Option_Preference::OPTION_DOOR]);
                    }
                    $preset_options[] = DHLPWC_Model_Meta_Order_Option_Preference::OPTION_BP;
                }
            }

            $priority_options = $option_service->filter_priority_options($preset_options);
            $non_priority_options = $option_service->filter_priority_options($preset_options, true);

            // Use only priority (delivery methods) options as base
            $preselected_options = $priority_options;

            // Add other preset options, if available
            foreach($non_priority_options as $option) {
                if ($option_service->check_exclusion($option, $order_id, $preselected_options, $to_business)) {
                    $option_service->add_key_to_stack($option, $preselected_options);
                }
            }

            // Add remaining defaults from settings, if available:
            // Default option settings
            $default_order_id_reference = $option_service->default_order_id_reference($order_id, $preselected_options, $to_business);
            if ($default_order_id_reference) {
                $option_service->add_key_to_stack(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE, $preselected_options);
            }

            // Default option settings
            $default_signature = $option_service->default_signature($order_id, $preselected_options, $to_business);
            if ($default_signature) {
                $option_service->add_key_to_stack(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_HANDT, $preselected_options);
            }

            // Default option settings
            $default_age_check = $option_service->default_age_check($order_id, $preselected_options, $to_business);
            if ($default_age_check) {
                $option_service->add_key_to_stack(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_AGE_CHECK, $preselected_options);
            }

            // Default option settings
            $default_insurance = $option_service->default_insurance($order_id, $preselected_options, $to_business);
            if ($default_insurance) {
                $option_service->add_key_to_stack(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_INS, $preselected_options);
            }

            // Default option settings
            $default_pers_note = $option_service->default_pers_note($order_id, $preselected_options, $to_business);
            if ($default_pers_note) {
                $option_service->add_key_to_stack(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PERS_NOTE, $preselected_options);
            }

            // Default option settings
            $default_return = $option_service->default_return($order_id, $preselected_options, $to_business);
            if ($default_return) {
                $option_service->add_key_to_stack(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_ADD_RETURN_LABEL, $preselected_options);
            }

            // Default option data
            $option_data = array();
            foreach($preselected_options as $preselected_option) {
                switch ($preselected_option) {
                    case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PS):
                        $order_meta_service = new DHLPWC_Model_Service_Order_Meta_Option();
                        $parcelshop = $order_meta_service->get_parcelshop($order_id);
                        $option_data[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PS] = $parcelshop->id;
                        break;
                    case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE):
                        $reference_value = apply_filters('dhlpwc_default_reference_value', $order_id, $order_id);
                        $option_data[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE] = $reference_value;
                        break;
                    case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE2):
                        $reference2_value = apply_filters('dhlpwc_default_reference2_value', $order_id, $order_id);
                        $option_data[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE2] = $reference2_value;
                        break;
                    case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_INS):
                        $insurance_value = $option_service->default_insurance_value();
                        $insurance_value = apply_filters('dhlpwc_default_insurance_value', $insurance_value, $order_id);
                        $option_data[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_INS] = $insurance_value;
                        break;
                }
            }

            // Generate sizes (with requested options)
            $service = DHLPWC_Model_Service_Access_Control::instance();
            $sizes = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_CAPABILITY_PARCELTYPE, array(
                'order_id' => $order_id,
                'options' => $preselected_options,
                'to_business' => $to_business,
            ));

            // Skip if no sizes are found
            if (empty($sizes)) {
                $bulk_fail++;
                continue;
            } else {
                $piece = [];
                switch($selected_size) {
                    case 'bp_only':
                    case 'smallest':
                        // Select smallest size available
                        $lowest_weight = null;
                        $smallest_dimensions = null;
                        foreach($sizes as $size) {
                            // Skip parcel-type ENVELOPE for 'bp_only'
                            if ($selected_size === 'bp_only' && strtolower($size->key) === 'envelope') {
                                continue;
                            }

                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            $size_dimensions = $size->dimensions->max_width_cm * $size->dimensions->max_length_cm * $size->dimensions->max_height_cm;
                            if (
                                $lowest_weight === null ||
                                $size->max_weight_kg < $lowest_weight ||
                                ($size->max_weight_kg === $lowest_weight && $size_dimensions < $smallest_dimensions)
                            ) {
                                $lowest_weight = $size->max_weight_kg;
                                $smallest_dimensions = $size_dimensions;
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                            }
                        }
                        break;
                    case 'envelope_only':
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            if (strtolower($size->key) === 'envelope') {
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                                break;
                            }
                        }


                        break;
                    case 'xsmall_only':
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            if (strtolower($size->key) === 'xsmall') {
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                                break;
                            }
                        }
                        break;
                    case 'small_only':
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            if (strtolower($size->key) === 'small') {
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                                break;
                            }
                        }
                        break;
                    case 'medium_only':
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            if (strtolower($size->key) === 'medium') {
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                                break;
                            }
                        }
                        break;
                    case 'xlarge_only':
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            if (strtolower($size->key) === 'xlarge') {
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                                break;
                            }
                        }
                        break;
                    case 'bulky_only':
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            if (strtolower($size->key) === 'bulky') {
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                                break;
                            }
                        }
                        break;
                    case 'largest':
                        // Select smallest size available
                        $highest_weight = null;
                        $biggest_dimensions = null;
                        foreach($sizes as $size) {
                            /** @var DHLPWC_Model_API_Data_Parceltype $size */
                            $size_dimensions = $size->dimensions->max_width_cm * $size->dimensions->max_length_cm * $size->dimensions->max_height_cm;
                            if (
                                $highest_weight === null ||
                                $size->max_weight_kg > $highest_weight ||
                                ($size->max_weight_kg === $highest_weight && $size_dimensions > $biggest_dimensions)
                            ) {
                                $highest_weight = $size->max_weight_kg;
                                $biggest_dimensions = $size_dimensions;
                                $piece['parcel_type'] = $size->key;
                                $piece['quantity'] = 1;
                            }
                        }
                        break;
                }
            }

            if (empty($piece)) {
                // Couldn't find an appropriate label size
                $bulk_fail++;
                continue;
            }

            $service = DHLPWC_Model_Service_Shipment::instance();
            $success = $service->create($order_id, [$piece], $preselected_options, $option_data, $to_business);

            if ($success) {
                $bulk_success++;
            } else {
                $bulk_fail++;
            }
        }

        return array(
            'success' => $bulk_success,
            'fail'    => $bulk_fail,
        );
    }

    /**
     * Delete a label attached to a specific order and with a specific label_id
     *
     * @param $order_id
     * @param $label_id
     */
    public function delete($order_id, $label_id)
    {
        $meta = new DHLPWC_Model_Service_Order_Meta();
        $label = $meta->delete_label($order_id, $label_id);
        if ($label) {
            $logic = DHLPWC_Model_Logic_Label::instance();
            $logic->delete_pdf_file($label['pdf']['path']);
        }
    }

    protected function clear_error($key)
    {
        if (array_key_exists($key, $this->errors)) {
            $this->errors[$key] = null;
            unset($this->errors[$key]);
        }
    }

    protected function set_error($key, $value)
    {
        $this->errors[$key] = $value;
    }

    public function get_error($key)
    {
        if (!array_key_exists($key, $this->errors)) {
            return null;
        }
        return $this->errors[$key];
    }

    protected function update_order_status($order_id)
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');
        if (!$shipping_method['change_order_status_to'] || $shipping_method['change_order_status_to'] === 'null') {
            return;
        }

        $order = wc_get_order($order_id);
        if (isset($shipping_method['change_order_status_from_wc-' . $order->get_status()]) && $shipping_method['change_order_status_from_wc-' . $order->get_status()] === 'yes') {
            $order->update_status($shipping_method['change_order_status_to']);
            $order->save();
        }

        return;
    }
}

endif;
