<?php
/**
 * Run the uninstall routine
 *
 * @package BPPV
 */

// if uninstall.php is not called by WordPress, die.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'bppv-api-key-invalid' );
delete_option( 'bppvp_settings' );
