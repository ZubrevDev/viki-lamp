<?php

if (!defined('ABSPATH')) { exit; }

if (!class_exists('DHLPWC_Controller_Admin_Settings')) :

class DHLPWC_Controller_Admin_Settings
{

    const NOTICE_TAG_PREFIX = 'dhlpwc_notice_';

    const NOTICE_TAG_COUNTRY = self::NOTICE_TAG_PREFIX . 'country';
    const NOTICE_TAG_API_SETTINGS = self::NOTICE_TAG_PREFIX . 'api_settings';

    public function __construct()
    {
        if (!is_admin()) {
            return;
        }

        add_action('admin_enqueue_scripts', array($this, 'load_styles'));
        add_action('admin_enqueue_scripts', array($this, 'load_scripts'));

        if (defined('DHLPWC_PLUGIN_BASENAME')) {
            add_filter('plugin_action_links_' . DHLPWC_PLUGIN_BASENAME, array($this, 'add_settings_link'), 10, 1);
        }

        add_filter('option_woocommerce_dhlpwc_settings', array($this, 'filter_settings_before_get'));
        add_filter('woocommerce_shipping_dhlpwc_instance_option', array($this, 'filter_settings_shipping_zones_before_get'));
        add_filter('pre_update_option_woocommerce_dhlpwc_settings', array($this, 'filter_settings_before_save'));
        add_filter('woocommerce_shipping_dhlpwc_instance_settings_values', array($this, 'filter_settings_before_save'));

        $service = DHLPWC_Model_Service_Access_Control::instance();
        if ($service->check(DHLPWC_Model_Service_Access_Control::ACCESS_SUBMENU_LINK)) {
            add_action('admin_menu', array($this, 'add_submenu_link'));
        }

        add_action('admin_notices', array($this, 'check_for_notices'));

        add_action('wp_ajax_dhlpwc_dismiss_admin_notice', array($this, 'dismiss_admin_notice'));
        add_action('wp_ajax_dhlpwc_test_connection', array($this, 'test_connection'));
        add_action('wp_ajax_dhlpwc_search_printers', array($this, 'search_printers'));
        add_action('wp_ajax_dhlpwc_dynamic_option_settings', array($this, 'dynamic_option_settings'));
    }

    public function add_submenu_link()
    {
        add_submenu_page(
            'woocommerce',
            __('DHL eCommerce for WooCommerce', 'dhlpwc'),
            __('DHL eCommerce for WooCommerce', 'dhlpwc'),
            'manage_options',
            'dhlpwc-menu-link',
            array($this, 'forward_settings_location')
        );
    }

    public function forward_settings_location()
    {
        wp_safe_redirect(admin_url('admin.php?page=wc-settings&tab=shipping&section=dhlpwc'));
    }

    public function dismiss_admin_notice()
    {
        $notice_tag = sanitize_text_field($_POST['notice_tag']);

        $json_response = new DHLPWC_Model_Response_JSON();

        if (substr($notice_tag, 0, strlen(self::NOTICE_TAG_PREFIX)) !== self::NOTICE_TAG_PREFIX) {
            $json_response->set_error(__('Unknown tag', 'dhlpwc'));
            wp_send_json($json_response->to_array(), 403);
            return;
        }

        // Remove prefix
        $value = true;
        $time = 7 * DAY_IN_SECONDS; // These are important messages, but we don't want to be too obnoxious. Make these messages return per week.
        set_site_transient($notice_tag, $value, $time);

        // Send JSON response
        wp_send_json($json_response->to_array(), 200);
    }

    public function test_connection()
    {
        $user_id = isset($_POST['user_id']) ? wc_clean($_POST['user_id']) : null;
        $key = isset($_POST['key']) ? wc_clean($_POST['key']) : null;

        $connector = DHLPWC_Model_API_Connector::instance();
        $authentication = $connector->test_authenticate($user_id, $key);

        // Send JSON response
        $json_response = new DHLPWC_Model_Response_JSON();
        $json_response->set_data(array(
            'success' => $authentication ? 'true' : 'false',
            'message' => $authentication ? __('Connection successful', 'dhlpwc') : __('Authentication failed', 'dhlpwc'),
            'info'    => $authentication,
        ));
        wp_send_json($json_response->to_array(), 200);
    }

    public function search_printers()
    {
        $service = DHLPWC_Model_Service_Printer::instance();
        $printers = $service->get_printers();

        // Send JSON response
        $json_response = new DHLPWC_Model_Response_JSON();
        $json_response->set_data(array(
            'success' => $printers ? 'true' : 'false',
            'message' => $printers ? __('Printers found', 'dhlpwc') : __('No printers found', 'dhlpwc'),
            'info'    => $printers,
        ));
        wp_send_json($json_response->to_array(), 200);
    }

    public function load_styles()
    {
        if ($this->is_plugin_screen() || $this->is_shipping_zone_screen() || $this->is_shipping_zone_instance_screen()) {
            wp_enqueue_style('dhlpwc-admin-style', DHLPWC_PLUGIN_URL . 'assets/css/dhlpwc.admin.css');
        }

        if ($this->is_plugin_screen()) {
            wp_enqueue_style('dhlpwc-admin_settings_only-style', DHLPWC_PLUGIN_URL . 'assets/css/dhlpwc.admin_settings_only.css');
        }
    }

    public function load_scripts()
    {
        wp_enqueue_script( 'dhlpwc-admin-notices', DHLPWC_PLUGIN_URL . 'assets/js/dhlpwc.notices.js', array('jquery'));

        if ($this->is_plugin_screen() || $this->is_shipping_zone_screen() || $this->is_shipping_zone_instance_screen()) {

            $condition_templates = array();
            $view = new DHLPWC_Template('admin.settings.condition.add-button');
            $condition_templates['add_button'] = $view->render(array(), false);

            $condition_service = DHLPWC_Model_Service_Condition_Rule::instance();
            $input_types = $condition_service->get_input_types();
            $input_actions = $condition_service->get_input_actions();

            $view = new DHLPWC_Template('admin.settings.condition.row');
            $condition_templates['row'] = $view->render(array(
                'input_types' => $input_types,
                'input_actions' => $input_actions,
            ), false);

            $view = new DHLPWC_Template('admin.settings.condition.table');
            $condition_templates['table'] = $view->render(array(), false);

            add_thickbox();
            wp_enqueue_script( 'dhlpwc-settings-action', DHLPWC_PLUGIN_URL . 'assets/js/dhlpwc.settings.js', array('jquery', 'jquery-ui-sortable'));
            wp_localize_script('dhlpwc-settings-action', 'dhlpwc_settings_object', array(
                'test_connection_message'          => __('Test connection and retrieve account data', 'dhlpwc'),
                'test_connection_loading_message'  => __('Please wait...', 'dhlpwc'),
                'accounts_found_message'           => __('Accounts found. Click to use.', 'dhlpwc'),
                'search_printers_message'          => __('Search for printers linked to account', 'dhlpwc'),
                'search_printers_loading_message'  => __('Please wait...', 'dhlpwc'),
                'printers_found_message'           => __('Printers found. Click to use.', 'dhlpwc'),
                'option_delivery_time_error'       => __('This needs to enabled together with the setup time frames in Delivery Times', 'dhlpwc'),
                'delivery_time_setup_error'        => __('This needs to be enabled together with the corresponding Delivery Option in Shipment Options', 'dhlpwc'),
                'snippet_description_message'      => esc_html(__('To customize this functionality, please use our filters.', 'dhlpwc')),
                'snippet_button_message'           => esc_html(__('Click here for an example snippet', 'dhlpwc')),
                'snippet_default_reference_value'  => DHLPWC_Model_Service_Snippets::instance()->default_order_reference(),
                'snippet_default_reference2_value' => DHLPWC_Model_Service_Snippets::instance()->default_order_reference2(),
                'same_day_info'                    => DHLPWC_Model_Service_Shipping_Preset::instance()->find_preset('same_day')->information,
                'next_day_info'                    => DHLPWC_Model_Service_Shipping_Preset::instance()->find_preset('same_day')->information,
                'no_neighbour_same_day_info'       => DHLPWC_Model_Service_Shipping_Preset::instance()->find_preset('no_neighbour_same_day')->information,
                'no_neighbour_next_day_info'       => DHLPWC_Model_Service_Shipping_Preset::instance()->find_preset('no_neighbour_next_day')->information,
                'condition_templates'              => $condition_templates,
                'currency_symbol'                  => get_woocommerce_currency_symbol(),
                'currency_pos'                     => get_option('woocommerce_currency_pos'),
                'weight_unit'                      => get_option('woocommerce_weight_unit'),
            ));
        }

        if ($this->is_plugin_screen()) {
            $locale = get_locale();
            $locale_parts = explode('_', $locale);
            $language = strtolower(reset($locale_parts));

            wp_enqueue_script( 'dhlpwc-settings-mopinion', DHLPWC_PLUGIN_URL . 'assets/js/dhlpwc.mopinion-loader.js');
            wp_localize_script('dhlpwc-settings-mopinion', 'dhlpwc_mopinion_object', array(
                'mopinion_js' => DHLPWC_PLUGIN_URL . 'assets/js/dhlpwc.mopinion.js',
                'form_key'    => 'd55f706e65bfae8311d009d8a1a63a3d86a411ee',
                'language'    => $language,
                'framework'   => 'woocommerce',
            ));
        }
    }

    protected function is_plugin_screen()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        if (!isset($screen)) {
            return false;
        }

        if ($screen->base !== 'woocommerce_page_wc-settings') {
            return false;
        }

        if (!isset($_GET['page']) || $_GET['page'] !== 'wc-settings') {
            return false;
        }

        if (!isset($_GET['tab']) || $_GET['tab'] !== 'shipping') {
            return false;
        }

        if (!isset($_GET['section']) || $_GET['section'] !== 'dhlpwc') {
            return false;
        }

        return true;
    }

    protected function is_shipping_zone_screen()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        if (!isset($screen)) {
            return false;
        }

        if ($screen->base !== 'woocommerce_page_wc-settings') {
            return false;
        }

        if (!isset($_GET['page']) || $_GET['page'] !== 'wc-settings') {
            return false;
        }

        if (!isset($_GET['tab']) || $_GET['tab'] !== 'shipping') {
            return false;
        }

        if (!isset($_GET['zone_id'])) {
            return false;
        }

        return true;
    }

    protected function is_shipping_zone_instance_screen()
    {
        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();
        if (!isset($screen)) {
            return false;
        }

        if ($screen->base !== 'woocommerce_page_wc-settings') {
            return false;
        }

        if (!isset($_GET['page']) || $_GET['page'] !== 'wc-settings') {
            return false;
        }

        if (!isset($_GET['tab']) || $_GET['tab'] !== 'shipping') {
            return false;
        }

        if (!isset($_GET['instance_id'])) {
            return false;
        }

        /** @var WC_Shipping_Method $shipping_method */
        $shipping_method = WC_Shipping_Zones::get_shipping_method($_GET['instance_id']);
        if (empty($shipping_method)) {
            return false;
        }

        if ($shipping_method->id !== 'dhlpwc') {
            return false;
        }

        return true;
    }

    public function check_for_notices()
    {
        $access_service = DHLPWC_Model_Service_Access_Control::instance();
        $service = DHLPWC_Model_Service_Settings::instance();

        $messages = array();

        // Check if the plugin is enabled, but not allowed to access the API
        if ($service->plugin_is_enabled() && !$access_service->check(DHLPWC_Model_Service_Access_Control::ACCESS_API)) {
            // Add general reminders
            if (!$service->country_is_set()) {
                $messages[] = sprintf(__('Missing %1$s from %2$s', 'dhlpwc'), __('Country / State', 'woocommerce'), __('General', 'woocommerce'));
            }
            if (!empty($messages) && !get_site_transient(self::NOTICE_TAG_COUNTRY)) {
                $this->show_notice(self::NOTICE_TAG_COUNTRY, $messages, admin_url('admin.php?page=wc-settings&tab=general'));
            }

            // Add plugin reminders
            $messages = array();
            if (empty($service->get_api_user())) {
                $messages[] = sprintf(__('Missing %1$s from %2$s', 'dhlpwc'), __('UserID', 'dhlpwc'), __('Account details', 'dhlpwc'));
            }
            if (empty($service->get_api_key())) {
                $messages[] = sprintf(__('Missing %1$s from %2$s', 'dhlpwc'), __('Key', 'dhlpwc'), __('Account details', 'dhlpwc'));
            }
            if (empty($service->get_api_account())) {
                $messages[] = sprintf(__('Missing %1$s from %2$s', 'dhlpwc'), __('AccountID', 'dhlpwc'), __('Account details', 'dhlpwc'));
            }
            if (!empty($messages) && !get_site_transient(self::NOTICE_TAG_API_SETTINGS)) {
                $this->show_notice(self::NOTICE_TAG_API_SETTINGS, $messages, admin_url('admin.php?page=wc-settings&tab=shipping&section=dhlpwc'));
            }

        }
    }

    public function show_notice($notice_tag, $messages, $admin_link = null)
    {
        // Add prefix if missing
        if (substr($notice_tag, 0, strlen(self::NOTICE_TAG_PREFIX)) !== self::NOTICE_TAG_PREFIX) {
            $notice_tag = self::NOTICE_TAG_PREFIX.$notice_tag;
        }

        $view = new DHLPWC_Template('admin.notice');
        $view->render(array(
            'notice_tag' => $notice_tag,
            'messages'   => $messages,
            'admin_link' => $admin_link,
        ));
    }

    public function add_settings_link($links)
    {
        $aria_label = __('View DHL eCommerce for WooCommerce settings', 'dhlpwc');
        $action_links = array(
            'settings' => '<a href="' . admin_url('admin.php?page=wc-settings&tab=shipping&section=dhlpwc') . '" aria-label="' . esc_attr($aria_label) . '">' . esc_html__('Settings', 'woocommerce') . '</a>',
        );

        return array_merge($action_links, $links);
    }

    public function filter_settings_before_get($data)
    {
        return DHLPWC_Model_Service_Settings::instance()->format_settings($data);
    }

    public function filter_settings_shipping_zones_before_get($data)
    {
        return DHLPWC_Model_Service_Settings::instance()->format_shipping_zones_settings($data);
    }

    public function filter_settings_before_save($data)
    {
        return DHLPWC_Model_Service_Settings::instance()->update_settings($data);
    }
}

endif;
