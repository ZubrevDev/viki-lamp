<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Model_WooCommerce_Settings_Shipping_Method')) :

class DHLPWC_Model_WooCommerce_Settings_Shipping_Method extends WC_Shipping_Method
{

    const ENABLE_FREE = 'enable_option_free';
    const ENABLE_TAX_ASSISTANCE = 'enable_tax_assistance';

    const PRICE_FREE = 'price_option_free';
    const FREE_AFTER_COUPON = 'free_after_coupon';
    const RULES_AFTER_FREE = 'rules_after_free';

    const PRESET_TRANSLATION_DOMAIN = 'preset_translation_domain';

    const SORT_COST_LOW = 'cost_low';
    const SORT_COST_HIGH = 'cost_high';
    const SORT_CUSTOM = 'custom';

    const DISPLAY_ZERO_NUMBER = 'display_zero_number';
    const DISPLAY_ZERO_TEXT = 'display_zero_text';

    const COMBINE_A4 = 'a4';

    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct($instance_id = 0)
    {
        parent::__construct($instance_id);

        $this->id = 'dhlpwc';
        $this->method_title = __('DHL eCommerce for WooCommerce', 'dhlpwc');
        $this->method_description = __('This is the official DHL Plugin for WooCommerce in WordPress. Do you have a WooCommerce webshop and are you looking for an easy way to process shipments within the Netherlands and abroad? This plugin offers you many options. You can easily create shipping labels and offer multiple delivery options in your webshop. Set up your account below.', 'dhlpwc');
        $this->instance_id = absint( $instance_id );
        $this->title = $this->method_title;
        $this->supports = array(
            'instance-settings',
            'settings',
        );
        if ($this->get_option('use_shipping_zones') === 'yes') {
            array_unshift($this->supports, 'shipping-zones');
        }
        $this->init();
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    public function init()
    {
        // Load the settings API
        $this->init_instance_form_fields();
        $this->init_form_fields();
        $this->init_settings();

        add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * @inheritdoc
     */
    public function get_admin_options_html() {
        return '<div id="dhlpwc_shipping_method_settings">' . parent::get_admin_options_html() . '</div>';
    }

    public function init_instance_form_fields()
    {
        if ($this->get_option('use_shipping_zones') === 'yes') {
            $this->instance_form_fields = $this->get_shipping_method_fields(false);
        } else {
            $this->instance_form_fields = array(
                'plugin_settings'              => array(
                    'title'       => __('Shipping Zones Settings', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Please enable Shipping Zones to use this feature.', 'dhlpwc'),
                ),
            );
        }
    }

    /**
     * Define settings field for this shipping
     * @return void
     */
    public function init_form_fields()
    {
        $country_code = wc_get_base_location();
        switch ($country_code['country']) {
            case 'NL':
                $api_settings_manual_url = 'https://www.dhlparcel.nl/sites/default/files/content/PDF/Handleiding_WooCommerce_koppeling_NL.pdf';
                break;
            default:
                $api_settings_manual_url = 'https://www.dhlparcel.nl/sites/default/files/content/PDF/Handleiding_WooCommerce_koppeling.v.2-EN.pdf';
        }

        $this->form_fields = array_merge(
            array(
                // Enable plugin
                'plugin_settings'              => array(
                    'title'       => __('Plugin Settings', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Enable features of this plugin.', 'dhlpwc'),
                ),
                'enable_all'                   => array(
                    'title'       => __('Enable plugin', 'dhlpwc'),
                    'type'        => 'select',
                    'options'     => array(
                        'yes'    => __('Live mode', 'dhlpwc'),
                        'test'    => __('Test mode', 'dhlpwc'),
                        'no'     => __('Off', 'dhlpwc'),
                    ),
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Setting the plugin mode to 'Test' allows you to test the plugin without API credentials. Please note that only fake labels will be created.", 'dhlpwc'),
                    'default'     => 'yes',
                ),
                'enable_submenu_link'          => array(
                    'title'       => __('Dashboard menu link', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Add a shortcut to the WooCommerce dashboard menu to quickly jump to DHL settings.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'enable_column_info'           => array(
                    'title'       => __('DHL label info', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Show', 'dhlpwc'),
                    'description' => __("Add shipping information in an additional column in your order overview.", 'dhlpwc'),
                    'default'     => 'yes',
                ),
                'open_label_links_external'    => array(
                    'title'       => __('Open admin label links in a new window', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Label actions like downloading PDF or opening track & trace will open in a new window.", 'dhlpwc'),
                    'default'     => 'yes',
                ),
                'enable_track_trace_mail' => array(
                    'title'       => __('Track & trace in email', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Add track & trace information to the default WooCommerce 'completed order' e-mail if available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'track_trace_mail_location' => array(
                    'title'   => __('Track & trace above or below order details', 'dhlpwc'),
                    'type'    => 'select',
                    'options' => array(
                        'before' => __('Before', 'dhlpwc'),
                        'after' => __('After', 'dhlpwc'),
                    ),
                    'default' => 'below',
                ),
                'custom_track_trace_mail_text' => array(
                    'title'       => __('Custom track & trace email text', 'dhlpwc'),
                    'type'        => 'textarea',
                    'placeholder' => sprintf('Once the shipment has been scanned, simply follow it with track & trace. Once the delivery is planned you will see the expected delivery time.'),
                    'description' => __("Leave empty to use default. Note: it's recommended to write this in English and pass through translation under the code 'dhlpwc'.", 'dhlpwc'),
                    'default'     => '',
                ),
                'enable_track_trace_component' => array(
                    'title'       => __('Track & trace info', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Show', 'dhlpwc'),
                    'description' => __("Include track & trace information in the order summary for customers, when they log into the website and check their account information.", 'dhlpwc'),
                    'default'     => 'yes',
                ),
                'enable_servicepoint_in_order_mail' => array(
                    'title'       => __('Servicepoint information in email', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Add servicepoint information below customer details in order emails', 'dhlpwc'),
                    'default'     => 'no',
                ),
                'servicepoint_mail_default_style' => array(
                    'title'       => __('Use default styling for ServicePoint', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Use default styling', 'dhlpwc'),
                    'description' => __('This will keep the ServicePoint information in the email communication in the same style as the billing and shipping address.', 'dhlpwc'),
                    'default'     => 'no',
                ),
                'google_maps_key' => array(
                    'title'       => __('Google Maps Javascript API key', 'dhlpwc'),
                    'type'        => 'text',
                    'placeholder' => sprintf(__('Example: %s', 'dhlpwc'), '1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6d7e8f90a'),
                    'description' => sprintf(
                        __('To show a visual map provided by Google Maps, please fill in your Google Maps Javascript API key. If unavailable, a regular selection box is shown without a map.%sNo Google Maps Javascript API credentials yet? Follow the instructions %shere%s on how to get the API key.', 'dhlpwc'),
                        '<br><br>',
                        '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">',
                        '</a>'
                    ),
                ),
            ),

            $this->get_order_status_change_fields(),

            array(
                'change_order_status_to' => array(
                    'type'    => 'select',
                    'options' => array_merge(
                        array('null' => __('Do not change order status', 'dhlpwc')),
                        array_map(array($this, 'change_order_status_to_option_update'), wc_get_order_statuses())
                    ),
                    'default' => 'null',
                ),

                // API settings
                'api_settings'                      => array(
                    'title'       => __('Account details', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => sprintf(
                        __('DHL API settings. Still missing API credentials? Follow the instructions %shere%s.', 'dhlpwc'),
                        '<a href="'.esc_url($api_settings_manual_url).'" target="_blank">',
                        '</a>'
                    ),
                ),
                'user_id'    => array(
                    'title'       => __('UserID', 'dhlpwc'),
                    'type'        => 'text',
                    'placeholder' => sprintf(__('Example: %s', 'dhlpwc'), '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d'),
                ),
                'key'        => array(
                    'title'       => __('Key', 'dhlpwc'),
                    'type'        => 'text',
                    'placeholder' => sprintf(__('Example: %s', 'dhlpwc'), '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d'),
                ),
                'test_connection' => array(
                    'title'       => __('Test connection', 'dhlpwc'),
                    'type'        => 'button',
                    'disabled'    => true,
                ),
                'account_id' => array(
                    'title'       => __('AccountID', 'dhlpwc'),
                    'type'        => 'text',
                    'placeholder' => sprintf(__('Example: %s', 'dhlpwc'), '01234567'),
                ),

                // Label settings
                'label_settings' => array(
                    'title'       => __('Label Settings', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Default label settings.', 'dhlpwc'),
                ),

                'check_default_send_signature' => array(
                    'title'       => __('Always enable required signature if available', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always select the signature option by default if the service is available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'check_default_age_check' => array(
                    'title'       => __('Always enable age check 18+ if available', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always select the age check 18+ option by default if the service is available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'check_default_pers_note' => array(
                    'title'       => __('Always enable message to the recipient if available', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always select the message to the recipient option by default if the service is available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'default_pers_note_input' => array(
                    'title'       => __('Default message to the recipient', 'dhlpwc'),
                    'type'        => 'text',
                    'placeholder' => __('Message'),
                    'description' => __('When creating a label, the message text will be pre-filled with this value.')
                ),
                'check_default_insurance' => array(
                    'title'       => __('Always enable shipment insurance to the recipient if available', 'dhlpwc'),
                    'type'        => 'select',
                    'options'     => array(
                        ''   => __('Disabled', 'dhlpwc'),
                        500  => __('Insure all shipments up to €500', 'dhlpwc'),
                        1000 => __('Insure all shipments up to €1000', 'dhlpwc'),
                        'custom_value' => __('More than €1000', 'dhlpwc'),
                    ),
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always add a shipment insurance option by default if the service is available.", 'dhlpwc'),
                    'default'     => '',
                    'class'       => 'dhlpwc-check-default-insurance-setting',
                ),
                'minimum_value_before_insurance' => array(
                    'title'       => __('Minimum order amount before enabling shipment insurance', 'dhlpwc'),
                    'type'        => 'text',
                    'description' => __("When creating a label, insurance will only automatically be applied when the order value is above this value.", 'dhlpwc'),
                    'class'       => 'dhlpwc-min-value-before-insurance-setting',
                ),
                'custom_insurance_value' => array(
                    'title'       => __('Shipment insurance amount', 'dhlpwc'),
                    'type'        => 'text',
                    'description' => __("Important: If the value of the goods exceeds €100.000, please contact our Customer Service prior to shipping.", 'dhlpwc'),
                    'class'       => 'dhlpwc-custom-insurance-value-setting',
                ),
                'check_default_order_id_reference' => array(
                    'title'       => __('Automatically add the order number as a reference, if possible', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always add the order number as reference by default if the service is available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'check_default_order_id_reference2' => array(
                    'title'       => __('Automatically add the order number as a second reference, if possible', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always add the order number as second reference by default if the service is available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'check_default_return' => array(
                    'title'       => __('Always enable return label if available', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When creating a label, always select the return label option by default if the service is available.", 'dhlpwc'),
                    'default'     => 'no',
                ),
                'bulk_container' => array(
                    'type'  => 'dhlpwc_bulk_container',
                ),
            ),

            $this->get_bulk_group_fields('smallest', __('Choose the smallest available size', 'dhlpwc')),
            $this->get_bulk_group_fields('envelope_only', sprintf(__("Choose size '%s' only, skip if unavailable", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->parcelType('PARCELTYPE_ENVELOPE'))),
            $this->get_bulk_group_fields('xsmall_only', sprintf(__("Choose size '%s' only, skip if unavailable", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->parcelType('PARCELTYPE_XSMALL'))),
            $this->get_bulk_group_fields('bp_only', __('Choose mailbox (0.5-2kg), skip if unavailable', 'dhlpwc')),
            $this->get_bulk_group_fields('small_only', sprintf(__("Choose size '%s' only, skip if unavailable", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->parcelType('PARCELTYPE_SMALL'))),
            $this->get_bulk_group_fields('medium_only', sprintf(__("Choose size '%s' only, skip if unavailable", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->parcelType('PARCELTYPE_MEDIUM'))),
            $this->get_bulk_group_fields('xlarge_only', sprintf(__("Choose size '%s' only, skip if unavailable", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->parcelType('PARCELTYPE_XLARGE'))),
            $this->get_bulk_group_fields('bulky_only', sprintf(__("Choose size '%s' only, skip if unavailable", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->parcelType('PARCELTYPE_BULKY'))),
            $this->get_bulk_group_fields('largest', __('Choose the largest available size', 'dhlpwc')),

            $this->get_bulk_services_fields(),

            array(
                'bulk_label_download' => array(
                    'title'       => __('Bulk label download', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'default'     => 'no',
                ),
                'bulk_label_combine' => array(
                    'title'   => __('Bulk labels combined for page printing', 'dhlpwc'),
                    'type'    => 'select',
                    'options' => array(
                        ''               => __('Default (1 label per page)', 'dhlpwc'),
                        self::COMBINE_A4 => __('Print 3 labels per page for an A4 paper sheet', 'dhlpwc'),
                    ),
                    'description' => __("When downloading labels with the bulk feature, labels can be combined to make it easier to print on a single sheet", 'dhlpwc'),
                    'default' => '',
                ),
                'validation_rule_address_number' => array(
                    'title'       => __('Validation rule: addresses require street number', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When activated, labels cannot be created for addresses without street number.", 'dhlpwc'),
                    'default'     => 'yes',
                ),

                // Shipment options
                'shipment_options_settings' => array(
                    'title'       => __('Shipment options', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __("Choose the shipment options for the recipients of your webshop.", 'dhlpwc'),
                ),

                'default_send_to_business' => array(
                    'title'       => __('Send to business by default', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("When enabled, by default labels will be created for business shipments and the checkout will show business shipping options.", 'dhlpwc'),
                    'default'     => 'no',
                ),

                self::PRESET_TRANSLATION_DOMAIN => array(
                    'title'       => __('Replace text label translation domain', 'dhlpwc'),
                    'type'        => 'text',
                    'description' => __("If using replacement text labels for shipping methods, it's possible to filter it with a translation domain. To use the text as-is, leave this field empty.", 'dhlpwc'),
                ),

                'display_zero_fee' => array(
                    'title'       => __('Display zero delivery fee price', 'dhlpwc'),
                    'type'        => 'select',
                    'options' => array(
                        ''                        => __('Default (no display)', 'dhlpwc'),
                        self::DISPLAY_ZERO_NUMBER => __('Display as numeric currency (based on your locale)', 'dhlpwc'),
                        self::DISPLAY_ZERO_TEXT   => sprintf(__('Display the text "(%s)"', 'dhlpwc'), __('Free', 'dhlpwc')),
                    ),
                    'default'     => '',
                ),

                'use_shipping_zones' => array(
                    'title'       => __('Use shipping zones', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Set shipping methods per shipping zone.", 'dhlpwc'),
                    'default'     => 'no',
                ),

                'custom_preset_sorting' => array(
                    'title'   => __('Change the sort behavior of shipment methods', 'dhlpwc'),
                    'type'    => 'select',
                    'options' => array(
                        ''                   => __('Default', 'dhlpwc'),
                        self::SORT_COST_LOW  => __('Sort by cost - lowest first', 'dhlpwc'),
                        self::SORT_COST_HIGH => __('Sort by cost - highest first', 'dhlpwc'),
                        self::SORT_CUSTOM    => __('Sort by custom sorting number', 'dhlpwc'),
                    ),
                    'default' => '',
                ),
            ),

            $this->get_shipping_method_fields(),

            array(
                // Delivery times
                'delivery_times_settings' => array(
                    'title'       => __('Delivery times', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Allow customers to select delivery times and manage when to send packages', 'dhlpwc'),
                ),
            ),

            $this->get_delivery_times_method_fields(),

            array(
                // Default shipping address
                'default_shipping_address_settings' => array(
                    'title'       => __('Default Shipping Address', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Fill in the details of your shipping address.', 'dhlpwc'),
                ),
            ),

            $this->get_address_fields(),

            array(
                'enable_alternate_return_address'                   => array(
                    'title'       => __('Different return address', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Use a different address for return labels.", 'dhlpwc'),
                    'default'     => 'no',
                ),
            ),

            $this->get_address_fields('return_address_'),

            array(
                'default_hide_sender_address'                   => array(
                    'title'       => __('Default hide sender address', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __("Set a default address for the 'Hide sender' service option.", 'dhlpwc'),
                    'default'     => 'no',
                ),
            ),

            $this->get_address_fields('hide_sender_address_'),

            array(
                // Printing
                'printer_settings' => array(
                    'title'       => __('Printer settings', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Got a Zebra printer? Download our DHL Printer Service app and print directly from WooCommerce!', 'dhlpwc'),
                ),
                'enable_printer' => array(
                    'title'       => __('Enable printer features', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Print labels directly with your Zebra printer from within the order view and order list.', 'dhlpwc'),
                ),
                'search_printers' => array(
                    'title'       => __('Search printers', 'dhlpwc'),
                    'type'        => 'button',
                    'disabled'    => true,
                ),
                'printer_id' => array(
                    'title'       => __('PrinterID', 'dhlpwc'),
                    'type'        => 'text',
                    'placeholder' => sprintf(__('Example: %s', 'dhlpwc'), '1a2b3c4d-5e6f-7a8b-9c0d-1e2f3a4b5c6d'),
                ),
                'enable_auto_print' => array(
                    'title'       => __('Enable auto printer features', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Automatically create and print labels when order has a specific status and has no labels already', 'dhlpwc'),
                ),
                'auto_print_on_status' => array(
                    'title'       => __('Auto print orders with this status', 'dhlpwc'),
                    'type'        => 'select',
                    'options'     => array_merge(
                        array('null' => __('Do not change order status', 'dhlpwc')),
                        array_map(array($this, 'on_status_to_option_update'), wc_get_order_statuses())
                    ),
                    'default'     => 'null',
                ),
            ),

            array(
                // Debug
                'developer_settings'                => array(
                    'title'       => __('Debug Settings', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Settings for developers.', 'dhlpwc'),
                ),
                'enable_debug'                      => array(
                    'title'       => __('Report errors', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Enable this and select one of the reporting methods below to automatically send errors of this plugin to the development team.', 'dhlpwc'),
                ),
                'enable_debug_mail'                 => array(
                    'title'       => __('By mail', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Errors will be automatically forwarded by e-mail.', 'dhlpwc'),
                ),
                'debug_url'                         => array(
                    'title'       => __('By custom URL', 'dhlpwc'),
                    'type'        => 'text',
                    'description' => __("Alternative API-URL. Used by developers. Will not be used if left empty (recommended).", 'dhlpwc'),
                ),
                'debug_external_url' => array(
                    'title'       => __('External custom URL', 'dhlpwc'),
                    'type'        => 'text',
                    'description' => __("Alternative secondary API-URL. Used by developers. Will not be used if left empty (recommended).", 'dhlpwc'),
                ),
                'enable_debug_requests' => array(
                    'title'       => __('Log Requests for debugging', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Enable this and you can check your request.', 'dhlpwc'),
                ),

                // Feedback
                'feedback_settings'                => array(
                    'title'       => __('Feedback', 'dhlpwc'),
                    'type'        => 'title',
                    'description' => __('Got questions or feedback about the plugin? Please let us know by clicking here.', 'dhlpwc'),
                ),
            )
        );
    }

    protected function get_option_group_fields($code, $title, $class = '')
    {
        $option_settings = array(
            'enable_option_' . $code => array(
                'title'             => __($title, 'dhlpwc'),
                'type'              => 'checkbox',
                'class'             => "dhlpwc-grouped-option dhlpwc-option-grid['" . $code . "'] " . $class,
                'default'           => 'no',
                'custom_attributes' => array(
                    'data-option-group' => $code,
                ),
            ),

            'price_option_' . $code => array(
                'type'              => 'price',
                'class'             => "dhlpwc-grouped-option dhlpwc-price-input dhlpwc-option-grid['" . $code . "'] " . $class,
                'default'           => '0.00',
                'custom_attributes' => array(
                    'data-option-group' => $code,
                ),
            ),

            'enable_free_option_' . $code => array(
                'type'              => 'checkbox',
                'class'             => "dhlpwc-grouped-option dhlpwc-option-grid['" . $code . "'] " . $class,
                'default'           => 'no',
                'custom_attributes' => array(
                    'data-option-group' => $code,
                ),
            ),

            'free_price_option_' . $code => array(
                'type'              => 'price',
                'class'             => "dhlpwc-grouped-option dhlpwc-price-input dhlpwc-option-grid['" . $code . "'] " . $class,
                'default'           => '0.00',
                'custom_attributes' => array(
                    'data-option-group' => $code,
                ),
            ),

            'alternative_option_text_' . $code => array(
                'type'              => 'text',
                'class'             => "dhlpwc-grouped-option dhlpwc-option-grid['" . $code . "'] " . $class,
                'default'           => '',
                'placeholder'       => __('Use default text label', 'dhlpwc'),
                'custom_attributes' => array(
                    'data-option-group' => $code,
                ),
            ),

            'sort_position_' . $code => array(
                'type'              => 'decimal',
                'class'             => "dhlpwc-grouped-option dhlpwc-option-grid['" . $code . "'] " . $class,
                'default'           => '',
                'placeholder'       => '0',
                'custom_attributes' => array(
                    'data-option-group' => $code,
                ),
            ),

            'option_condition_' . $code => array(
                'type'    => 'textarea',
                'class'   => 'dhlpwc-option-condition ' . $class,
                'default' => '',
            ),
        );

        return $option_settings;
    }

    protected function get_bulk_services_fields()
    {
        $service = DHLPWC_Model_Service_Access_Control::instance();
        $bulk_services = $service->check(DHLPWC_Model_Service_Access_Control::ACCESS_BULK_SERVICES, true);

        $bulk_service_fields = array(
            'bulk_services_container' => array(
                'type'  => 'dhlpwc_bulk_services_container',
            ),
        );

        foreach ($bulk_services as $bulk_service) {
            $bulk_service_fields = array_merge($bulk_service_fields, $this->get_bulk_service_group_fields('service_' . $bulk_service, sprintf(__("Show service: '%s'", 'dhlpwc'), DHLPWC_Model_Service_Translation::instance()->option(strtoupper($bulk_service)))));
        }

        return $bulk_service_fields;
    }

    protected function get_shipping_method_fields($is_global = true)
    {
        if ($is_global) {
            $class = 'dhlpwc-global-shipping-setting';
        } else {
            $class = 'dhlpwc-instance-shipping-setting';
        }

        $option_settings = array(
            self::ENABLE_TAX_ASSISTANCE => array(
                'title'       => __('Enter prices with tax included', 'dhlpwc'),
                'type'        => 'checkbox',
                'description' => __("Turn this on to enter prices with the tax included. Turn this off to enter prices without tax.", 'dhlpwc'),
                'class'       => $class,
                'default'     => 'yes',
            ),

            self::ENABLE_FREE => array(
                'title'       => __('Free or discounted shipping', 'dhlpwc'),
                'type'        => 'checkbox',
                'label'       => __('Enable', 'dhlpwc'),
                'description' => __("Offer free shipping (over a certain amount).", 'dhlpwc'),
                'class'       => $class,
                'default'     => 'no',
            ),

            self::PRICE_FREE       => array(
                'title'             => __('Free or discounted shipping threshold', 'dhlpwc'),
                'type'              => 'price',
                'description'       => __("Free or discounted shipping prices are applied when the total price is over the inputted value.", 'dhlpwc'),
                'default'           => '0.00',
                'class'             => 'dhlpwc-price-input ' . $class,
            ),

            self::FREE_AFTER_COUPON => array(
                'title'       => __('Free or discounted shipping and coupons', 'dhlpwc'),
                'type'        => 'checkbox',
                'label'       => __('Calculate after applying coupons', 'dhlpwc'),
                'description' => __("Calculate eligibility for free or discounted shipping after applying coupons.", 'dhlpwc'),
                'class'       => $class,
                'default'     => 'no',
            ),

            self::RULES_AFTER_FREE => array(
                'title'       => __('Apply additional rules after free or discount calculation', 'dhlpwc'),
                'type'        => 'checkbox',
                'label'       => __('Calculate additional rules after free or discount', 'dhlpwc'),
                'description' => __("When checked, rules will apply after free or discount calculation. When unchecked, rules will be applied first and ends with free or discount calculation.", 'dhlpwc'),
                'class'       => $class,
                'default'     => 'no',
            ),

        );

        $service = DHLPWC_Model_Service_Shipping_Preset::instance();
        $presets = $service->get_presets();

        $option_settings['grouped_option_container'] = array(
            'type'  => 'dhlpwc_grouped_option_container',
        );

        foreach($presets as $data) {
            $preset = new DHLPWC_Model_Meta_Shipping_Preset($data);
            $option_settings = array_merge($option_settings, $this->get_option_group_fields($preset->setting_id, $preset->title, $class));
        }

        return $option_settings;
    }

    protected function get_delivery_times_method_fields()
    {
        $service = DHLPWC_Model_Service_Shipping_Preset::instance();
        $same_day = $service->find_preset('same_day');
        $home = $service->find_preset('home');
        $no_neighbour_same_day = $service->find_preset('no_neighbour_same_day');
        $no_neighbour = $service->find_preset('no_neighbour');
        $next_day = $service->find_preset('next_day');
        $no_neighbour_next_day = $service->find_preset('no_neighbour_next_day');

        return array_merge(
            array(
                'enable_delivery_times' => array(
                    'title'       => __('Enable delivery times', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Show delivery date and time selection in the checkout and show delivery dates in the dashboard.', 'dhlpwc'),
                ),
                'delivery_times_default' => array(
                    'title'       => __('Default time window selection', 'dhlpwc'),
                    'type'        => 'select',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Show same day as a time window option, not as separate shipping method.', 'dhlpwc'),
                    'options'     => array(
                                        DHLPWC_Model_Service_Delivery_Times::DEFAULT_SELECTION_HOME => 'First option for home delivery (default)',
                                        DHLPWC_Model_Service_Delivery_Times::DEFAULT_SELECTION_SDD => 'Same day, if available'
                                     ),
                    'default'     => DHLPWC_Model_Service_Delivery_Times::DEFAULT_SELECTION_HOME,
                ),
                'same_day_as_time_window' => array(
                    'title'       => __('Show same day delivery as delivery time', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'description' => __('Show same day as a time window option, not as separate shipping method.', 'dhlpwc'),
                    'default'     => 'yes',
                ),
                'enable_delivery_times_stock_check' => array(
                    'title'       => __('Check stock', 'dhlpwc'),
                    'type'        => 'checkbox',
                    'label'       => __('Enable', 'dhlpwc'),
                    'default'     => 'yes',
                    'description' => __('Only show delivery times when all cart items are in stock.', 'dhlpwc'),
                ),
                'delivery_times_number_of_days' => array(
                    'title'       => __('Number of days to display', 'dhlpwc'),
                    'type'        => 'select',
                    'options'     => $this->get_number_of_days(),
                    'description' => __("Number of days to display for delivery times", 'dhlpwc'),
                    'default'     => 14,
                ),
                'delivery_times_container' => array(
                    'type'  => 'dhlpwc_delivery_times_container',
                ),
            ),

            $this->get_delivery_times_group_fields($same_day->setting_id, sprintf(__('%s available until', 'dhlpwc'), $same_day->title), true),
            $this->get_delivery_times_group_fields($no_neighbour_same_day->setting_id, sprintf(__('%s available until', 'dhlpwc'), $no_neighbour_same_day->title), true),
            $this->get_delivery_times_group_fields($home->setting_id, sprintf(__('Regular %s available until', 'dhlpwc'), $home->title)),
            $this->get_delivery_times_group_fields($no_neighbour->setting_id, sprintf(__('Regular %s available until', 'dhlpwc'), $no_neighbour->title)),
            $this->get_delivery_times_group_fields($next_day->setting_id, sprintf(__('%s available until', 'dhlpwc'), $next_day->title), true, true),
            $this->get_delivery_times_group_fields($no_neighbour_next_day->setting_id, sprintf(__('%s available until', 'dhlpwc'), $no_neighbour_next_day->title), true, true),

            $this->get_shipping_days()
        );
    }

    protected function get_number_of_days()
    {
        $days = array();
        for ($day = 1; $day <= 14; $day++) {
            $days[$day] = sprintf(_n('Display %s day', 'Display %s days', $day, 'dhlpwc'), $day);
        }
        return $days;
    }

    protected function get_shipping_days()
    {
        $days = array(
            'monday'    => __('Monday', 'dhlpwc'),
            'tuesday'   => __('Tuesday', 'dhlpwc'),
            'wednesday' => __('Wednesday', 'dhlpwc'),
            'thursday'  => __('Thursday', 'dhlpwc'),
            'friday'    => __('Friday', 'dhlpwc'),
            'saturday'  => __('Saturday', 'dhlpwc'),
            'sunday'    => __('Sunday', 'dhlpwc'),
        );

        $defaults = array(
            'monday'    => 'yes',
            'tuesday'   => 'yes',
            'wednesday' => 'yes',
            'thursday'  => 'yes',
            'friday'    => 'yes',
            'saturday'  => 'no',
            'sunday'    => 'no',
        );

        $shipping_days = array();
        foreach($days as $day => $day_text) {
            $shipping_days['enable_shipping_day_' . $day] = array(
                'title'       => sprintf(__('Ship on %ss', 'dhlpwc'), $day_text),
                'type'        => 'checkbox',
                'label'       => __('Enable', 'dhlpwc'),
                'default'     => $defaults[$day],
            );
        }

        return $shipping_days;
    }

    protected function get_days_for_sending()
    {
        $days = range(1, 14);

        $list = array();
        foreach ($days as $day) {
            if ($day === 1) {
                $list[$day] = __('Next day', 'dhlpwc');
            } else {
                $list[$day] = sprintf(__('%s day', 'dhlpwc'), $day);
            }
        }

        return $list;
    }

    protected function get_time_for_sending($ceil = 24)
    {
        $hours = range(1, $ceil);

        $list = array();
        foreach ($hours as $hour) {
            if ($hour === 24) {
                $list[$hour] = '23:59';
            } else {
                $list[$hour] = sprintf('%s:00', $hour);
            }
        }

        return $list;
    }

    protected function get_delivery_times_group_fields($code, $title, $skip_day_select = false, $starting_from_select = false)
    {
        $time_ceiling = $skip_day_select && !$starting_from_select ? 18 : 24;

        $options = array(
            'enable_delivery_time_' . $code  => array(
                'type'              => 'checkbox',
                'class'             => "dhlpwc-delivery-times-option dhlpwc-delivery-times-grid['" . $code . "']",
                'default'           => 'no',
                'custom_attributes' => array(
                    'data-delivery-times-group' => $code,
                ),
            ),
            'delivery_day_cut_off_' . $code  => array(
                'type'              => 'select',
                'class'             => "dhlpwc-delivery-times-option dhlpwc-delivery-times-grid['" . $code . "']",
                'options'           => $this->get_days_for_sending(),
                'custom_attributes' => array(
                    'data-delivery-times-group' => $code,
                ),
            ),
            'delivery_time_starting_from_' . $code  => array(
                'title'             => __('Starting from', 'dhlpwc'),
                'type'              => 'select',
                'class'             => "dhlpwc-delivery-times-option dhlpwc-delivery-times-grid['" . $code . "']",
                'options'           => $this->get_time_for_sending(),
                'default'           => 16,
                'custom_attributes' => array(
                    'data-delivery-times-group' => $code,
                ),
            ),
            'delivery_time_cut_off_' . $code => array(
                'title'             => $title,
                'type'              => 'select',
                'class'             => "dhlpwc-delivery-times-option dhlpwc-delivery-times-grid['" . $code . "']",
                'options'           => $this->get_time_for_sending($time_ceiling),
                'default'           => $starting_from_select ? 22 : 16,
                'custom_attributes' => array(
                    'data-delivery-times-group' => $code,
                ),
            ),
        );

        if ($skip_day_select) {
            $options['delivery_day_cut_off_' . $code] = null;
            unset($options['delivery_day_cut_off_' . $code]);
        }

        if (!$starting_from_select) {
            $options['delivery_time_starting_from_' . $code] = null;
            unset($options['delivery_time_starting_from_' . $code]);
        }

        return $options;
    }

    protected function get_bulk_group_fields($code, $title)
    {
        return array(
            'enable_bulk_option_' . $code  => array(
                'title'             => __($title, 'dhlpwc'),
                'type'              => 'checkbox',
                'class'             => "dhlpwc-bulk-option dhlpwc-bulk-grid['" . $code . "']",
                'default'           => 'no',
                'custom_attributes' => array(
                    'data-bulk-group' => $code,
                ),
            )
        );
    }

    protected function get_bulk_service_group_fields($code, $title)
    {
        return array(
            'enable_bulk_option_' . $code  => array(
                'title'             => __($title, 'dhlpwc'),
                'type'              => 'checkbox',
                'class'             => "dhlpwc-bulk-service-option dhlpwc-bulk-service-grid['" . $code . "']",
                'default'           => 'no',
                'custom_attributes' => array(
                    'data-bulk-service-group' => $code,
                ),
            )
        );
    }

    public function get_address_fields($prefix = null)
    {
        switch($prefix) {
            case 'return_address_':
                $class = 'dhlpwc-return-address-setting';
                break;
            case 'hide_sender_address_':
                $class = 'dhlpwc-hide-sender-address-setting';
                break;
            default:
                $class = 'dhlpwc-default-address-setting';
        }

        $settings = array(
            $prefix.'first_name'                        => array(
                'title' => __('First Name', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'last_name'                         => array(
                'title' => __('Last Name', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'company'                           => array(
                'title' => __('Company', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),

            $prefix.'postcode'                          => array(
                'title' => __('Postcode', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'city'                              => array(
                'title' => __('City', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'street'                            => array(
                'title' => __('Street', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'number'                            => array(
                'title' => __('Number', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'addition'                            => array(
                'title' => __('Addition', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'country' => array(
                'title' => __('Country', 'dhlpwc'),
                'type' => 'select',
                'options' => array(
                    'NL' => __('Netherlands', 'dhlpwc'),
                    'BE' => __('Belgium', 'dhlpwc'),
                    'LU' => __('Luxembourg', 'dhlpwc'),
                ),
                'default' => 'NL',
                'class' => $class,
            ),
            $prefix.'email'                             => array(
                'title' => __('Email', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
            $prefix.'phone'                             => array(
                'title' => __('Phone', 'dhlpwc'),
                'type'  => 'text',
                'class' => $class,
            ),
        );

        // Remove country settings for SSN
        if ($prefix == 'hide_sender_address_') {
            $settings[$prefix.'country'] = null;
            unset($settings[$prefix.'country']);
        }

        return $settings;
    }

    public function calculate_shipping($package = array())
    {
        // Skip calculation if plugin is not enabled
        if ($this->get_option('enable_all') !== 'yes' &&  $this->get_option('enable_all') !== 'test') {
            return;
        }

        $domain = $this->get_option(self::PRESET_TRANSLATION_DOMAIN);

        $service = DHLPWC_Model_Service_Shipping_Preset::instance();
        $presets = $service->get_presets();

        $access_service = DHLPWC_Model_Service_Access_Control::instance();
        $allowed_shipping_options = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_CAPABILITY_OPTIONS);

        // Exception for Next Day SDD
        $same_day_allowed_unfiltered = false;
        if (array_key_exists(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD, $allowed_shipping_options)) {
            $same_day_allowed_unfiltered = true;
        }

        // When using delivery times and it is not showing (out of stock, unsupported country, or unavailable), don't allow same day delivery to show
        if ($access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DELIVERY_TIMES)) {
            $delivery_times_active = $access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_DELIVERY_TIMES_ACTIVE);

            if (!$delivery_times_active) {
                if (array_key_exists(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD, $allowed_shipping_options)) {
                    $allowed_shipping_options[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD] = null;
                    unset($allowed_shipping_options[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD]);
                }
            }
        } else {
            $delivery_times_service = DHLPWC_Model_Service_Delivery_Times::instance();

            if (!$delivery_times_service->same_day_without_delivery_times_allowed()) {
                if (array_key_exists(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD, $allowed_shipping_options)) {
                    $allowed_shipping_options[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD] = null;
                    unset($allowed_shipping_options[DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD]);
                }
            }
        }

        // remove methods that have not been allowed for products which have restriction turned on
        foreach ($package['contents'] as $item_id => $item) {
            if (get_post_meta($item['product_id'], 'dhlpwc_enable_method_limit', true) === 'yes') {
                $allowed_methods = get_post_meta($item['product_id'], 'dhlpwc_selected_method_limit', true);

                // When no method should be used, create an empty array instead of empty string or null
                if (empty($allowed_methods)) {
                    $allowed_methods = array();
                }

                foreach ($presets as $method_key => $method) {
                    if (in_array($method['frontend_id'], $allowed_methods) === false) {
                        unset($presets[$method_key]);
                    }
                }
            }
        }

        foreach ($presets as $data) {

            $preset = new DHLPWC_Model_Meta_Shipping_Preset($data);

            $check_allowed_options = true;

            foreach ($preset->options as $preset_option) {
                // Exemption for next-day
                if (strpos($preset->frontend_id, 'next-day') !== false && $preset_option === DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD && $same_day_allowed_unfiltered) {
                    $no_neighbour = in_array(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_NBB, $preset->options);

                    $delivery_times_service = DHLPWC_Model_Service_Delivery_Times::instance();
                    if (!$delivery_times_service->next_day_allowed_today($no_neighbour)) {
                        $check_allowed_options = false;
                    }
                    continue;
                }

                if (!array_key_exists($preset_option, $allowed_shipping_options)) {
                    $check_allowed_options = false;
                    continue;
                }

                if ($preset_option === DHLPWC_Model_Meta_Order_Option_Preference::OPTION_SDD) {
                    $no_neighbour = in_array(DHLPWC_Model_Meta_Order_Option_Preference::OPTION_NBB, $preset->options);

                    $delivery_times_service = DHLPWC_Model_Service_Delivery_Times::instance();
                    if (!$delivery_times_service->same_day_allowed_today($no_neighbour)) {
                        $check_allowed_options = false;
                    }
                }
            }

            if ($this->get_option('enable_option_' . $preset->setting_id) === 'yes' && $check_allowed_options === true) {

                $alternate_text = $this->get_option('alternative_option_text_' . $preset->setting_id);
                if (!empty($alternate_text)) {
                    if (!empty($domain)) {
                        $title = __($alternate_text, $domain);
                    } else {
                        $title = $alternate_text;
                    }
                } else {
                    $title = __($preset->title, 'dhlpwc');
                }

                $cost = $this->calculate_cost($package, $preset->setting_id);
                // Apply negative tax if tax assistance is turned on (so WooCommerce can apply tax on it properly)
                if ($this->get_option(self::ENABLE_TAX_ASSISTANCE) === 'yes') {
                    $taxes = WC_Tax::calc_inclusive_tax($cost, WC_Tax::get_shipping_tax_rates());
                    foreach($taxes as $tax) {
                        $cost -= $tax;
                    }
                }

                // Pass sort position meta data if available
                $meta_data = array();
                if ($sort_position = $this->get_option('sort_position_' . $preset->setting_id)) {
                    $meta_data = array('sort_position' => $sort_position);
                }

                if (!$this->disable_condition($preset->setting_id, $package)) {
                    $this->add_rate(array(
                        'id'        => 'dhlpwc-' . $preset->frontend_id,
                        'label'     => $title,
                        'cost'      => $cost,
                        'meta_data' => $meta_data,
                    ));
                }

            }
        }
    }

    protected function generate_dhlpwc_bulk_container_html($key, $data)
    {
        $view = new DHLPWC_Template('admin.settings.bulk-header');
        return $view->render(array(), false);
    }

    protected function generate_dhlpwc_bulk_services_container_html($key, $data)
    {
        $view = new DHLPWC_Template('admin.settings.bulk-services-header');
        return $view->render(array(), false);
    }

    protected function generate_dhlpwc_delivery_times_container_html($key, $data)
    {
        $view = new DHLPWC_Template('admin.settings.delivery-times-header');
        return $view->render(array(), false);
    }

    protected function generate_dhlpwc_grouped_option_container_html($key, $data)
    {
        $view = new DHLPWC_Template('admin.settings.options-grid-header');
        return $view->render(array(), false);
    }

    protected function calculate_cost($package = array(), $option = null)
    {
        $price = floatval($this->get_option('price_option_' . $option));

        // Apply condition rules before free/discount
        if ($this->get_option(self::RULES_AFTER_FREE) !== 'yes') {
            $price = floatval($this->price_conditions($price, $option, $package));
        }

        // Free/discount price calculation
        $free_or_discounted = $this->get_option(self::ENABLE_FREE) === 'yes' && $this->get_subtotal_price($package) >= $this->get_option(self::PRICE_FREE);
        // Allow developers to add other conditions to trigger free & discount prices
        $free_or_discounted = apply_filters('dhlpwc_use_discount_price', $free_or_discounted, $option, $package);

        if ($free_or_discounted) {
            if ($this->get_option('enable_free_option_' . $option) === 'yes') {
                $price = $this->get_free_price($option);
            }
        }

        // Apply condition rules after free/discount
        if ($this->get_option(self::RULES_AFTER_FREE) === 'yes') {
            $price = floatval($this->price_conditions($price, $option, $package));
        }

        $price += $this->get_additional_shipping_fee($package);

        // Allow developers to manipulate the calculated price
        return apply_filters('dhlpwc_calculate_price', $price, $free_or_discounted, $option, $package);
    }

    protected function get_additional_shipping_fee($package = array())
    {
        $additional_shipping_fee = 0;

        foreach ($package['contents'] as $item) {
            $additional_shipping_fee += (floatval(get_post_meta($item['product_id'], 'dhlpwc_additional_shipping_fee', true)) * $item['quantity']);
        }

        return $additional_shipping_fee;
    }

    protected function price_conditions($price, $option, $package)
    {
        $conditions = $this->get_option('option_condition_' . $option);
        $service = DHLPWC_Model_Service_Condition_Rule::instance();

        // Allow developers to manipulate price conditions
        $conditions = apply_filters('dhlpwc_price_conditions', $conditions, $option);

        $price = $service->calculate_price($price, $conditions, $this->get_subtotal_price($package));
        return $price;
    }

    protected function disable_condition($option, $package)
    {
        $conditions = $this->get_option('option_condition_' . $option);
        $service = DHLPWC_Model_Service_Condition_Rule::instance();

        // Allow developers to manipulate disable conditions
        $conditions = apply_filters('dhlpwc_disable_conditions', $conditions, $option);

        $disabled = $service->is_disabled($conditions, $this->get_subtotal_price($package));
        return $disabled;
    }

    protected function get_free_price($option)
    {
        return round(floatval($this->get_option('free_price_option_' . $option)), wc_get_price_decimals());
    }

    protected function get_subtotal_price($package = array())
    {
        if ($this->get_option(self::FREE_AFTER_COUPON) === 'yes') {
            $subtotal = 0;
            foreach($package['contents'] as $key => $order)
            {
                $subtotal += $order['line_total'] + $order['line_tax'];
            }
            return round($subtotal, wc_get_price_decimals());
        }

        return $package['cart_subtotal'];
    }

    protected function change_order_status_to_option_update($option)
    {
        return sprintf(__('Change status to: %s', 'dhlpwc'), $option);
    }

    protected function on_status_to_option_update($option)
    {
        return sprintf(__('On status: %s', 'dhlpwc'), $option);
    }

    protected function get_order_status_change_fields()
    {
        $fields = array ();
        $order_statuses = wc_get_order_statuses();
        $add_title = true;
        foreach ($order_statuses as $order_status_key => $order_status_label) {
            $fields['change_order_status_from_' . $order_status_key] = array(
                'type'        => 'checkbox',
                'label'       => sprintf(__('Change status if order is: %s', 'dhlpwc'), $order_status_label),
                'default'     => 'no',
            );
            if ($add_title) {
                $fields['change_order_status_from_' . $order_status_key]['title'] = __('Apply status change when creating a label', 'dhlpwc');
                $add_title = false;
            }
        }

        return $fields;
    }
}

endif;
