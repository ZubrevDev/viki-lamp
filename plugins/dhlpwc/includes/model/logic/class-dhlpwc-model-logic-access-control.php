<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Model_Logic_Access_Control')) :

class DHLPWC_Model_Logic_Access_Control extends DHLPWC_Model_Core_Singleton_Abstract
{

    public function check_enabled()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_all'])) {
            return false;
        }

        if ($shipping_method['enable_all'] != 'yes' && !$this->is_test_mode()) {
            return false;
        }

        return true;
    }

    public function is_test_mode()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_all'])) {
            return false;
        }

        return $shipping_method['enable_all'] === 'test';
    }

    public function check_auto_print()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_auto_print'])) {
            return false;
        }

        if ($shipping_method['enable_auto_print'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_submenu_link()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_submenu_link'])) {
            return false;
        }

        if ($shipping_method['enable_submenu_link'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_application_country()
    {
        $country_code = wc_get_base_location();

        if (!in_array($country_code['country'], array(
            'NL',
            'BE',
            'LU',
        ))) {
            return false;
        }

        return true;
    }

    public function check_account()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (empty($shipping_method['user_id'])) {
            return false;
        }

        if (empty($shipping_method['key'])) {
            return false;
        }

        if (empty($shipping_method['account_id'])) {
            return false;
        }

        return true;
    }

    public function check_default_shipping_address()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (!empty($shipping_method)) {
            return false;
        }

        if (empty($shipping_method['first_name'])) {
            return false;
        }

        if (empty($shipping_method['last_name'])) {
            return false;
        }

        if (empty($shipping_method['company'])) {
            return false;
        }

        if (empty($shipping_method['country'])) {
            return false;
        }

        if (empty($shipping_method['postcode'])) {
            return false;
        }

        if (empty($shipping_method['city'])) {
            return false;
        }

        if (empty($shipping_method['street'])) {
            return false;
        }

        if (empty($shipping_method['number'])) {
            return false;
        }

        if (empty($shipping_method['email'])) {
            return false;
        }

        if (empty($shipping_method['phone'])) {
            return false;
        }

        return true;

    }

    public function check_column_info()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_column_info'])) {
            return false;
        }

        if ($shipping_method['enable_column_info'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_open_label_links_external()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['open_label_links_external'])) {
            return false;
        }

        if ($shipping_method['open_label_links_external'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_bulk_create()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        $bulk_options = array(
            'smallest',
            'bp_only',
            'envelope_only',
            'xsmall_only',
            'small_only',
            'medium_only',
            'xlarge_only',
            'bulky_only',
            'largest',
        );

        $enabled = array();

        foreach($bulk_options as $bulk_option) {
            if (isset($shipping_method['enable_bulk_option_' . $bulk_option]) && $shipping_method['enable_bulk_option_' . $bulk_option] == 'yes') {
                $enabled[] = $bulk_option;
            }
        }

        if (!count($enabled)) {
            return null;
        }

        return $enabled;
    }

    public function check_bulk_services($all = false)
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return array();
        }

        $bulk_services = array(
            DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD
        );

        $bulk_services = array_map('strtolower', $bulk_services);

        if ($all) {
            return $bulk_services;
        }

        $enabled = array();

        foreach($bulk_services as $bulk_service) {
            if (isset($shipping_method['enable_bulk_option_service_' . $bulk_service]) && $shipping_method['enable_bulk_option_service_' . $bulk_service] == 'yes') {
                $enabled[] = $bulk_service;
            }
        }

        if (!count($enabled)) {
            return array();
        }

        return $enabled;
    }

    public function check_bulk_download()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['bulk_label_download'])) {
            /** Legacy support, older setting before renaming printing to download */
            return $this->legacy_check_bulk_download();
        }

        if ($shipping_method['bulk_label_download'] != 'yes') {
            return false;
        }

        return true;
    }

    protected function legacy_check_bulk_download()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (!isset($shipping_method['bulk_label_printing'])) {
            return false;
        }

        if ($shipping_method['bulk_label_printing'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_bulk_combine()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (empty($shipping_method['bulk_label_combine'])) {
            return false;
        }

        return $shipping_method['bulk_label_combine'];
    }

    public function check_track_trace_mail()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_track_trace_mail'])) {
            return false;
        }

        if ($shipping_method['enable_track_trace_mail'] != 'yes') {
            return false;
        }

        return true;
    }

    public function track_trace_mail_location()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['track_trace_mail_location'])) {
            return false;
        }

        return $shipping_method['track_trace_mail_location'];
    }

    public function check_track_trace_mail_text()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_track_trace_mail'])) {
            return false;
        }

        if ($shipping_method['enable_track_trace_mail'] != 'yes') {
            return false;
        }

        if (empty($shipping_method['custom_track_trace_mail_text'])) {
            return false;
        }

        return sanitize_text_field($shipping_method['custom_track_trace_mail_text']);

    }

    public function check_track_trace_component()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_track_trace_component'])) {
            return false;
        }

        if ($shipping_method['enable_track_trace_component'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_service_point_in_order_mail()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_servicepoint_in_order_mail'])) {
            return false;
        }

        if ($shipping_method['enable_servicepoint_in_order_mail'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_service_point_default_style()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');
        if (!isset($shipping_method['servicepoint_mail_default_style'])) {
            return false;
        }

        if ($shipping_method['servicepoint_mail_default_style'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_debug()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_debug'])) {
            return false;
        }

        if ($shipping_method['enable_debug'] != 'yes') {
            return false;
        }

        if (empty($shipping_method['debug_url'])) {
            return false;
        }

        if (filter_var($shipping_method['debug_url'], FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return $shipping_method['debug_url'];
    }

    public function check_debug_external()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_debug'])) {
            return false;
        }

        if ($shipping_method['enable_debug'] != 'yes') {
            return false;
        }

        if (empty($shipping_method['debug_external_url'])) {
            return false;
        }

        if (filter_var($shipping_method['debug_external_url'], FILTER_VALIDATE_URL) === false) {
            return false;
        }

        return $shipping_method['debug_external_url'];
    }

    public function check_debug_mail()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_debug'])) {
            return false;
        }

        if ($shipping_method['enable_debug'] != 'yes') {
            return false;
        }

        if (!isset($shipping_method['enable_debug_mail'])) {
            return false;
        }

        if ($shipping_method['enable_debug_mail'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_default_send_to_business()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['default_send_to_business'])) {
            return false;
        }

        if ($shipping_method['default_send_to_business'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_display_zero_fee($type = 'number')
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['display_zero_fee'])) {
            return false;
        }

        // Number check
        if ($type === 'number') {
            if ($shipping_method['display_zero_fee'] != DHLPWC_Model_WooCommerce_Settings_Shipping_Method::DISPLAY_ZERO_NUMBER) {
                return false;
            }

            return true;
        }

        // Text check
        if ($shipping_method['display_zero_fee'] != DHLPWC_Model_WooCommerce_Settings_Shipping_Method::DISPLAY_ZERO_TEXT) {
            return false;
        }

        return true;
    }

    public function check_default_send_signature()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_send_signature'])) {
            return false;
        }

        if ($shipping_method['check_default_send_signature'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_default_age_check()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_age_check'])) {
            return false;
        }

        if ($shipping_method['check_default_age_check'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_default_insurance($args = null)
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_insurance'])) {
            return false;
        }

        if ($args && isset($args['order_id'])) {
            $order = new WC_Order($args['order_id']);

            if (isset($shipping_method['minimum_value_before_insurance']) && $order->get_subtotal() < $shipping_method['minimum_value_before_insurance']) {
                return false;
            }
        }

        if ($shipping_method['check_default_insurance'] != 500 && $shipping_method['check_default_insurance'] != 1000 && $shipping_method['check_default_insurance'] != 'custom_value') {
            return false;
        }

        return true;
    }


    public function check_default_pers_note()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_pers_note'])) {
            return false;
        }

        if ($shipping_method['check_default_pers_note'] != 'yes') {
            return false;
        }

        return true;
    }

    public function default_pers_note_input()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return '';
        }

        if (!isset($shipping_method['default_pers_note_input'])) {
            return '';
        }

        return $shipping_method['default_pers_note_input'];
    }

    public function check_default_order_id_reference()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_order_id_reference'])) {
            return false;
        }

        if ($shipping_method['check_default_order_id_reference'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_default_order_id_reference2()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_order_id_reference2'])) {
            return false;
        }

        if ($shipping_method['check_default_order_id_reference2'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_default_return()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['check_default_return'])) {
            return false;
        }

        if ($shipping_method['check_default_return'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_custom_sort()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (empty($shipping_method['custom_preset_sorting'])) {
            return false;
        }

        return $shipping_method['custom_preset_sorting'];
    }

    public function check_shipping_preset_enabled($code)
    {
        if (!is_string($code)) {
            return false;
        }

        if (!in_array($code, array(
            'home',
            'evening',
            'same_day',
            'next_day',
            'no_neighbour',
            'no_neighbour_evening',
            'no_neighbour_same_day',
            'no_neighbour_next_day',
        ))) {
            return false;
        }

        return $this->check_zone_option_enabled($code);
    }

    public function check_parcelshop_enabled()
    {
        return $this->check_zone_option_enabled('parcelshop');
    }

    protected function check_zone_option_enabled($option)
    {
        $cart = WC()->cart;
        if (!$cart) {
            return false;
        }

        if (is_callable(array($cart, 'get_customer'))) {
            // WooCommerce 3.2.0+
            $customer = $cart->get_customer();
        } else {
            // WooCommerce < 3.2.0
            $customer = WC()->customer;
        }

        if (!$customer) {
            return false;
        }

        if (!$customer->get_shipping_country()) {
            return false;
        }

        $shipping_methods = WC_Shipping::instance()->load_shipping_methods(array(
            'destination' => array(
                'country'  => $customer->get_shipping_country(),
                'state'    => $customer->get_shipping_state(),
                'postcode' => $customer->get_shipping_postcode(),
            ),
        ));

        if (empty($shipping_methods)) {
            return false;
        }

        $continue = false;
        foreach($shipping_methods as $shipping_method) {
            if ($shipping_method->id === 'dhlpwc') {
                $continue = true;
                break;
            }
        }

        if (!$continue) {
            return false;
        }

        /** @var DHLPWC_Model_WooCommerce_Settings_Shipping_Method $shipping_method */
        if ($shipping_method->get_option('enable_option_' . $option) !== 'yes') {
            return false;
        }

        return true;
    }

    public function check_delivery_times_enabled()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_delivery_times'])) {
            return false;
        }

        if ($shipping_method['enable_delivery_times'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_same_day_as_time_window()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return true;
        }

        if (!isset($shipping_method['same_day_as_time_window'])) {
            return true;
        }

        if ($shipping_method['same_day_as_time_window'] == 'no') {
            return false;
        }

        return true;
    }

    public function check_delivery_times_active()
    {
        if (!$this->check_delivery_times_enabled()) {
            return false;
        }

        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        // Consumers only
        if ($this->check_default_send_to_business()) {
            return false;
        }

        $cart = WC()->cart;
        if (!$cart) {
            return false;
        }

        // Do not show for carts with no shipping required

        if (is_callable(array($cart, 'get_customer'))) {
            // WooCommerce 3.2.0+
            $customer = $cart->get_customer();
        } else {
            // WooCommerce < 3.2.0
            $customer = WC()->customer;
        }

        if (!$customer) {
            return false;
        }

        if (!$customer->get_shipping_country()) {
            return false;
        }

        if (!isset($shipping_method['country'])) {
            return false;
        }

        // Same country only
        if ($customer->get_shipping_country() != $shipping_method['country']) {
            return false;
        }

        // Stock check (if enabled)
        if (isset($shipping_method['enable_delivery_times_stock_check']) && $shipping_method['enable_delivery_times_stock_check'] == 'yes') {
            $cart_content = $cart->get_cart();

            if (!$cart_content) {
                return false;
            }

            $out_of_stock = false;
            foreach ($cart_content as $cart_item_key => $cart_item) {
                if (!$cart_item['data']->is_in_stock()) {
                    $out_of_stock = true;
                    break;

                } else if ($cart_item['data']->is_on_backorder()) {
                    $out_of_stock = true;
                    break;
                } else if ($cart_item['data']->backorders_allowed()) {
                    $stock_info = $cart_item['data']->get_stock_quantity();
                    if ($stock_info < $cart_item['quantity']) {
                        $out_of_stock = true;
                        break;
                    }
                }
            }

            if ($out_of_stock) {
                return false;
            }

        }

        return true;
    }

    public function check_shipping_day($day)
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        $days = array(
            'monday',
            'tuesday',
            'wednesday',
            'thursday',
            'friday',
            'saturday',
            'sunday',
        );

        if (!is_string($day) || !in_array($day, $days)) {
            return false;
        }

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_shipping_day_' . $day])) {
            return false;
        }

        if ($shipping_method['enable_shipping_day_' . $day] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_alternate_return_address()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_alternate_return_address'])) {
            return false;
        }

        if ($shipping_method['enable_alternate_return_address'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_default_hide_sender_address()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['default_hide_sender_address'])) {
            return false;
        }

        if ($shipping_method['default_hide_sender_address'] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_printer()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if (!isset($shipping_method['enable_printer'])) {
            return false;
        }

        if ($shipping_method['enable_printer'] != 'yes') {
            return false;
        }

        if (empty($shipping_method['printer_id'])) {
            return false;
        }

        return true;
    }

    public function check_label_request()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
          return false;
        }

        if (!isset($shipping_method['enable_debug'])) {
          return false;
        }

        if ($shipping_method['enable_debug'] != 'yes') {
          return false;
        }

        if (!isset($shipping_method['enable_debug_requests'])) {
          return false;
        }

        if ($shipping_method['enable_debug_requests'] != 'yes') {
          return false;
        }

        return true;
    }

    public function check_validation_rule($identifier)
    {
        if (!is_string($identifier)) {
            return false;
        }

        $default_on = array(
            'address_number',
            'address_country',
        );

        $default_off = array(
        );

        if (!in_array($identifier, $default_on) && !in_array($identifier, $default_off)) {
            return false;
        }

        $return_boolean = boolval(in_array($identifier, $default_on));

        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return $return_boolean;
        }

        if (!isset($shipping_method['validation_rule_' . $identifier])) {
            return $return_boolean;
        }

        if ($return_boolean) {
            if ($shipping_method['validation_rule_' . $identifier] != 'no') {
                return true;
            }

            return false;
        }

        if ($shipping_method['validation_rule_' . $identifier] != 'yes') {
            return false;
        }

        return true;
    }

    public function check_use_shipping_zones()
    {
        $shipping_method = get_option('woocommerce_dhlpwc_settings');

        if (empty($shipping_method)) {
            return false;
        }

        if ($shipping_method['use_shipping_zones'] != 'yes') {
            return false;
        }

        return true;
    }
}

endif;

