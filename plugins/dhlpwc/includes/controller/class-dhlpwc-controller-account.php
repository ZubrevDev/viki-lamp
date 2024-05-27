<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Controller_Account')) :

class DHLPWC_Controller_Account
{

    public function __construct()
    {
        $service = DHLPWC_Model_Service_Access_Control::instance();
        if ($service->check(DHLPWC_Model_Service_Access_Control::ACCESS_TRACK_TRACE_COMPONENT)) {
            add_action('woocommerce_order_details_after_order_table_items', array($this, 'track_and_trace'), 10, 1);
        }
    }

    public function track_and_trace($wc_order)
    {
        /** @var WC_Order $wc_order **/
        $locale = str_replace('_', '-', get_locale());

        $service = DHLPWC_Model_Service_Order_Meta::instance();
        $country_code = $service->get_country_code($wc_order->get_id());

        $service = DHLPWC_Model_Service_Track_Trace::instance();
        $tracking_codes = $service->get_track_trace_from_order($wc_order->get_id());

        $tracking_urls = [];
        foreach($tracking_codes as $tracking_code) {
            $service = DHLPWC_Model_Service_Track_Trace::instance();
            $tracking_urls[$tracking_code] = $service->get_url($tracking_code, $locale, $country_code);
        }

        $view = new DHLPWC_Template('track-and-trace');

        $view->render(array(
            'tracking_urls' => $tracking_urls
        ));
    }

}

endif;
