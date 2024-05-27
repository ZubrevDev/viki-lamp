<?php
/**
 * The Byteplant phone validator start file. Here we do initialize the plugin.
 *
 * Plugin Name: Byteplant Phone Validator
 * Plugin URI: https://www.phone-validator.net/
 * Description: Phone validation plugin. Works with Contact Form 7, Gravity Forms, WPForms, Ninja Forms and WooCommerce. For other 3rd party forms: add class='bppvp-phone' to all input fields you want to validate.
 * Version: 4.1
 * License: GPL2
 * Author: Byteplant
 * Author URI: https://www.phone-validator.net/
 * Text Domain: byteplant-phone-validator
 * Domain Path: /languages
 *
 * @package Plugins
 **/

require_once( dirname( __FILE__ ) . '/src/functions.php' );
require_once( dirname( __FILE__ ) . '/src/class-bppv-plugin.php' );
add_action( 'after_setup_theme', 'bppv_load', 11 );

$plugin_data = get_file_data(__FILE__, array('Version' => 'Version'), false);
$plugin_version = $plugin_data['Version'];

define ( 'BPPV_PLUGIN_CURRENT_VERSION', $plugin_version );

/**
 * Initialize the plugin
 *
 * @return void
 */
function bppv_load() {

	$plugin = BPPV_Plugin::get_instance();
	$plugin->plugin_setup();
}
