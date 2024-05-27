<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Model_Service_Label_Metabox')) :

    class DHLPWC_Model_Service_Label_Metabox extends DHLPWC_Model_Core_Singleton_Abstract
    {

        // TODO: temporarily use a whitelist system due not all options being fully supported yet
        // Either missing input, or not tested, especially country specific options
        protected $whitelist = array(
            // Delivery option
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PS,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_DOOR,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_BP,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_H, // (requires custom address selection)

            // Service option
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_EXP,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_BOUW,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE2,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_EXW,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_EA,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_EVE,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_RECAP,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_INS,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_HANDT,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_NBB,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_ADD_RETURN_LABEL,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SSN,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PERS_NOTE,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_S,
            //DHLPWC_Model_Meta_Order_Option_Preference::OPTION_IS_BULKY,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_AGE_CHECK,
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_BMC,
        );

        public function order_labels($order_id, $labels)
        {
            $content = '';
            if (!empty($labels)) {
                foreach ($labels as $label) {
                    $view = new DHLPWC_Template('order.meta.label');
                    $is_return = (!empty($label['is_return'])) ? $label['is_return'] : false;
                    $content .= $view->render(array(
                        'label_size'        => $label['label_size'],
                        'label_description' => DHLPWC_Model_Service_Translation::instance()->parcelType($label['label_size']),
                        'tracker_code'      => $label['tracker_code'],
                        'is_return'         => $is_return,
                        'actions'           => $this->get_label_actions($label, $order_id),
                    ), false);
                }
            } else {
                $view = new DHLPWC_Template('order.meta.no-label');
                $content .= $view->render(array(), false);
            }

            $view = new DHLPWC_Template('order.meta.label-container');
            return $view->render(array(
                'content' => $content
            ), false);
        }

        public function private_or_business_form($checked)
        {
            $view = new DHLPWC_Template('order.meta.form.to-business');
            return $view->render(array(
                'checked' => $checked,
            ), false);
        }

        public function options_form($order_id, $selected_options, $option_data, $to_business)
        {
            $service = DHLPWC_Model_Service_Access_Control::instance();
            $allowed_options = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_CAPABILITY_ORDER_OPTIONS, array(
                'order_id' => $order_id,
                'to_business' => $to_business,
            ));

            $option_service = DHLPWC_Model_Service_Order_Meta_Option::instance();

            // Filter response to options
            $delivery_options = array();
            $service_options = array();
            if ($allowed_options) {
                $service = DHLPWC_Model_Service_Shipment_Option::instance();

                foreach ($allowed_options as $allowed_option) {
                    // TODO temporarily use a whitelist
                    if (!in_array($allowed_option->key, $this->whitelist)) {
                        continue;
                    }

                    /* @var DHLPWC_Model_API_Data_Option $option */
                    $option = $allowed_option;
                    $option->image_url = $service->get_image_url($option->key);
                    $option->description = DHLPWC_Model_Service_Translation::instance()->option($option->key);
                    $option->exclusion_list = array();

                    // Making a special case: H is also a delivery_type
                    if ($option->key === DHLPWC_Model_Meta_Order_Option_Preference::OPTION_H) {
                        $option->option_type = DHLPWC_Model_API_Data_Option::OPTION_TYPE_DELIVERY;
                    }

                    if (!empty($option->exclusions)) {
                        foreach($option->exclusions as $exclusion) {
                            $option->exclusion_list[] = $exclusion->key;
                        }
                    }

                    if (in_array($option->key, $selected_options)) {
                        $option->preselected = true;
                    }

                    // Update input template
                    switch($option->key) {
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE):
                            if (!empty($option_data) && is_array($option_data) && array_key_exists(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE, $option_data)) {
                                $logic = DHLPWC_Model_Logic_Shipment::instance();
                                $value = $logic->get_reference_data($option_data);
                            } else {
                                $access_service = DHLPWC_Model_Service_Access_Control::instance();
                                $default_order_id_reference = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DEFAULT_ORDER_ID_REFERENCE);

                                if ($default_order_id_reference) {
                                    $value = apply_filters('dhlpwc_default_reference_value', $order_id, $order_id);
                                } else {
                                    $value = null;
                                }
                            }

                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_TEXT;
                            $option->input_template_data = array(
                                'placeholder' => __('Reference', 'dhlpwc'),
                                'value' => $value,
                            );
                            break;
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE2):
                            if (!empty($option_data) && is_array($option_data) && array_key_exists(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE2, $option_data)) {
                                $logic = DHLPWC_Model_Logic_Shipment::instance();
                                $value = $logic->get_reference2_data($option_data);
                            } else {
                                $access_service = DHLPWC_Model_Service_Access_Control::instance();
                                $default_order_id_reference2 = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DEFAULT_ORDER_ID_REFERENCE2);

                                if ($default_order_id_reference2) {
                                    $value = apply_filters('dhlpwc_default_reference2_value', $order_id, $order_id);
                                } else {
                                    $value = null;
                                }
                            }

                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_TEXT;
                            $option->input_template_data = array(
                                'placeholder' => __('Second reference', 'dhlpwc'),
                                'value' => $value,
                            );
                            break;
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PERS_NOTE):
                            $access_service = DHLPWC_Model_Service_Access_Control::instance();
                            $default_pers_note_input = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DEFAULT_PERS_NOTE_INPUT);

                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_TEXT;
                            $option->input_template_data = array(
                                'placeholder' => __('Message', 'dhlpwc'),
                                'value' => $default_pers_note_input,
                            );
                            break;
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_PS):
                            $order_meta_service = new DHLPWC_Model_Service_Order_Meta_Option();
                            $parcelshop = $order_meta_service->get_parcelshop($order_id);

                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_PARCELSHOP;
                            $option->input_template_data = array(
                                'parcelshop' => $parcelshop,
                            );
                            break;
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_INS):
                            if (!empty($option_data) && is_array($option_data)) {
                                $logic = DHLPWC_Model_Logic_Shipment::instance();
                                $value = $logic->get_insurance_data($option_data);
                            } else {
                                $default_insurance_value = $option_service->default_insurance_value();
                                $default_insurance_value = apply_filters('dhlpwc_default_insurance_value', $default_insurance_value, $order_id);

                                if (is_numeric($default_insurance_value) && $default_insurance_value > 0) {
                                    $value = $default_insurance_value;
                                } else {
                                    $value = null;
                                }
                            }

                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_PRICE;
                            $option->input_template_data = array(
                                'placeholder' => __('In euros (€)', 'dhlpwc'),
                                'value' => $value
                            );
                            break;
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SSN):
                            if (!empty($option_data) && is_array($option_data) && array_key_exists(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SSN, $option_data)) {
                                $logic = DHLPWC_Model_Logic_Shipment::instance();
                                $hide_sender_data = $logic->get_hide_sender_data($option_data);
                                if ($logic->validate_flat_address($hide_sender_data)) {
                                    $hide_sender_address = new DHLPWC_Model_Meta_Address($hide_sender_data);
                                } else {
                                    $hide_sender_address = null;
                                }
                            } else {
                                $access_service = DHLPWC_Model_Service_Access_Control::instance();
                                $default_hide_sender = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DEFAULT_HIDE_SENDER_ADDRESS);

                                if ($default_hide_sender) {
                                    $settings_service = DHLPWC_Model_Service_Settings::instance();
                                    $hide_sender_address = $settings_service->get_hide_sender_address();
                                } else {
                                    $hide_sender_address = null;
                                }
                            }

                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_ADDRESS;
                            $option->input_template_data = array(
                                'address' => $hide_sender_address,
                            );
                            break;
                        case (DHLPWC_Model_Meta_Order_Option_Preference::OPTION_H):
                            $option->input_template = DHLPWC_Model_API_Data_Option::INPUT_TEMPLATE_TERMINAL;
                            $option->input_template_data = array();
                            break;
                        default:
                            $option->input_template = null;
                            $option->input_template_data = null;
                    }

                    if (!empty($option->input_template)) {
                        $view = new DHLPWC_Template('order.meta.form.input.' . $option->input_template);
                        $option->input_template = $view->render($option->input_template_data, false);
                    }

                    if ($option->option_type === DHLPWC_Model_API_Data_Option::OPTION_TYPE_DELIVERY) {
                        // Making a special case: DOOR goes first
                        if ($option->key === DHLPWC_Model_Meta_Order_Option_Preference::OPTION_DOOR) {
                            array_unshift($delivery_options, $option);
                        } else {
                            $delivery_options[] = $option;
                        }
                    } else {
                        // Make a special case: references go first (to group them)
                        if ($option->key === DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE ||
                            $option->key === DHLPWC_Model_Meta_Order_Option_Preference::OPTION_REFERENCE2) {
                            array_unshift($service_options, $option);
                        } else {
                            $service_options[] = $option;
                        }
                    }

                }
            } else {
                $messages = DHLPWC_Model_Core_Flash_Message::instance();
                $messages->add_error(__('No matching capabilities found.', 'dhlpwc'), 'dhlpwc_label_meta');
            }

            $view = new DHLPWC_Template('order.meta.form.options');
            return $view->render(array(
                'delivery_options' => $delivery_options,
                'service_options' => $service_options,
            ), false);
        }

        public function sizes_header($selected_options)
        {
            $view = new DHLPWC_Template('order.meta.form.sizes-headline');
            $option_texts = $selected_options;
            $option_texts = array_map(
                function ($value) {
                    return DHLPWC_Model_Service_Translation::instance()->option($value);
                },
                $option_texts
            );

            return $view->render(array(
                'message' => implode(' + ', $option_texts),
            ), false);
        }

        public function sizes_form($order_id, $selected_options, $to_business)
        {
            $size_view = '';
            $service = DHLPWC_Model_Service_Access_Control::instance();
            $sizes = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_CAPABILITY_PARCELTYPE, array(
                'order_id' => $order_id,
                'options' => $selected_options,
                'to_business' => $to_business,
            ));

            if (!empty($sizes)) {
                foreach ($sizes as $size) {
                    $view = new DHLPWC_Template('order.meta.form.size');
                    $size->display_weight = $this->get_weight_display($size);
                    $size_view .= $view->render(array(
                        'parceltype' => $size,
                        'description' => DHLPWC_Model_Service_Translation::instance()->parcelType($size->key)
                    ), false);

                }
            } else {
                $view = new DHLPWC_Template('order.meta.form.no-sizes');
                $size_view .= $view->render(array(), false);
            }

            return $size_view;
        }

        protected function get_weight_display($size)
        {
            $unit = 'kg';
            $display_min_weight = round($size->min_weight_grams / 1000, 3);
            $display_max_weight = round($size->max_weight_grams / 1000, 3);
            if ($size->max_weight_grams < 1000) {
                return sprintf('min %s %s, max %s %s', $display_min_weight, $unit, $display_max_weight, $unit);
            }

            return sprintf('max %s %s', $display_max_weight, $unit);
        }

        protected function get_label_actions($label, $post_id)
        {
            $locale = str_replace('_', '-', get_locale());

            $service = DHLPWC_Model_Service_Order_Meta::instance();
            $country_code = $service->get_country_code($post_id);

            $service = DHLPWC_Model_Service_Track_Trace::instance();
            $tracking_url = $service->get_url($label['tracker_code'], $locale, $country_code);

            $service = DHLPWC_Model_Service_Access_Control::instance();
            $printer = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_PRINTER);
            $debug_label_requests = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_LABEL_REQUEST);

            $logic = DHLPWC_Model_Logic_Label::instance();

            $actions = array();
            $actions[] = array(
                'url'    => $logic->get_pdf_url($label),
                'name'   => __('Download PDF label', 'dhlpwc'),
                'action' => "dhlpwc_action_download",
            );

            if ($printer) {
                $actions[] = array(
                    'url'    => admin_url('post.php?post=' . $post_id . '&action=edit'),
                    'name'   => __('Print PDF label', 'dhlpwc'),
                    'action' => "dhlpwc_action_print",
                );
            }

            $actions[] = array(
                'url'    => esc_url($tracking_url),
                'name'   => __('Follow track & trace', 'dhlpwc'),
                'action' => "dhlpwc_action_follow_tt",
            );

            $actions[] = array(
                'url'    => admin_url('post.php?post=' . $post_id . '&action=edit'),
                'name'   => __('Delete PDF label', 'dhlpwc'),
                'action' => "dhlpwc_action_delete",
            );

            if ($debug_label_requests && !empty($label['request'])) {
                $actions[] = array(
                    'url'           => admin_url('admin-ajax.php?action=dhlpwc_print_label_request&post_id=' . $post_id . '&label_id=' . $label['label_id']),
                    'name'          => __('Show Label Request', 'dhlpwc'),
                    'action'        => "dhlpwc_action_request",
                    'external_link' => false
                );
            }

            // Create template
            $action_view = '';

            $service = DHLPWC_Model_Service_Access_Control::instance();
            $external_link = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_OPEN_LABEL_LINKS_EXTERNAL);

            foreach ($actions as $action) {
                $view = new DHLPWC_Template('admin.action-button');
                $action_view .= $view->render(array(
                    'action'        => $action,
                    'post_id'       => $post_id,
                    'label_id'      => $label['label_id'],
                    'external_link' => isset($action['external_link']) ? $action['external_link'] : $external_link,
                ), false);
            }

            $view = new DHLPWC_Template('admin.action-button-container');
            return $view->render(array(
                'content'  => $action_view,
            ), false);
        }

    }

endif;
