<?php
/**
 * In this file you find functions
 *
 * @package BPPV
 */

/**
 * Use this function in your plugin or theme to activate frontend Ajax API requests.
 * An input field with the class .bppvp-phone will be validated before the form gets send.
 */
function bppv_activate_third_party() {

	// The ajax request endpoint is public now.
	add_filter( 'bppv_api_is_private', '__return_false' );
	add_action( 'wp_enqueue_scripts', array( BPPV_Plugin::get_instance(), 'enqueue_frontend' ), 11 );
	add_action( 'wp_footer', array( BPPV_Plugin::get_instance(), 'footer_styles' ) );
}
