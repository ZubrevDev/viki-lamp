<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Model_Service_Track_Trace')) :

class DHLPWC_Model_Service_Track_Trace extends DHLPWC_Model_Core_Singleton_Abstract
{
    protected $url = 'https://www.dhlparcel.nl/en/follow-your-shipment?tc={{trackerCode}}';
    protected $alternate_urls = [
        'NL' => 'https://my.dhlparcel.nl/home/tracktrace/{{trackerCode}}?lang={{locale}}',
        'BE' => 'https://www.dhlparcel.be/nl/particulieren/volg-je-zending?tt={{trackerCode}}'
    ];

    public function get_url($tracking_code = null, $locale = null, $country_code = null)
    {
        if ($tracking_code !== null) {
            $tracking_code = urlencode($tracking_code);
        }

        $language = 'en-NL';
        if ($locale !== null && substr(urlencode($locale), 0, 2) === 'nl') {
            $language = 'nl-NL';
        }

        $tracking_url = $this->url;
        if (array_key_exists(strtoupper($country_code), $this->alternate_urls)) {
            $tracking_url = $this->alternate_urls[strtoupper($country_code)];
        }

        return str_replace(array('{{trackerCode}}', '{{locale}}'), array($tracking_code, $language), $tracking_url);
    }

    public function get_track_trace_from_order($order_id)
    {
        $service = DHLPWC_Model_Service_Order_Meta::instance();
        $labels = $service->get_labels($order_id);

        if (!$labels || !is_array($labels)) {
            return array();
        }

        $tracker_codes = array();
        foreach($labels as $label) {
            if (array_key_exists('tracker_code', $label) && empty($label['is_return'])) {
                $tracker_codes[] = $label['tracker_code'];
            }
        }

        return $tracker_codes;
    }

}

endif;
