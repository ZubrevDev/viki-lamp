<?php
/**
 * Handles the phone validation for WooCommerce.
 *
 * @package BPPV
 */

/**
 * Class BPPV_WC
 */
class BPPV_WC {

	/**
	 * The validator object
	 *
	 * @var object
	 */
	protected $validator = NULL;


	/**
	 * BPPV_WC constructor.
	 */
	public function __construct() {}

	/**
	 * Set up the handler.
	 */
	public function setup() {

		add_action( 'woocommerce_after_checkout_validation', array( $this, 'validate_checkout' ) );
		add_filter( 'woocommerce_process_myaccount_field_billing_phone', array( $this, 'validate_edit' ) );
	}

	/**
	 * Validate the phone number on checkout.
	 *
	 * @param array $fields The fields of the checkout.
	 */
	public function validate_checkout( $fields ) {

		if ( ! empty( $fields['billing_phone'] ) && WC_Validation::is_phone( $fields['billing_phone'] ) ) {
			$phone_number = trim($fields['billing_phone']);
			$this->validator->set_phone( $phone_number );
			if ( strlen( $phone_number ) > 2 && substr( $phone_number, 0, 2 ) !== '00' && substr( $phone_number, 0, 1 ) !== '+' )
				$country_code = $fields['billing_country']; // number in local format, set country to billing country
			else $country_code = '';
			$is_valid = $this->validator->validate( $country_code );
			if ( ! $is_valid ) {
				wc_add_notice( __( '<strong>Phone</strong> is not a valid phone number.', 'byteplant-phone-validator' ), 'error' );
			}
		}
	}

	/**
	 * Validate the phone number on edit address.
	 * This works right now via a filter, since we do not have a sufficient action hook.
	 *
	 * @param mixed $phone The phone number to validate.
	 *
	 * @return string $phone
	 */
	public function validate_edit( $phone ) {

		if ( ! empty( $phone ) && WC_Validation::is_phone( $phone ) ) {
			$this->validator->set_phone( $phone );
			$is_valid = $this->validator->validate();
			if ( ! $is_valid ) {
				wc_add_notice( __( '<strong>Phone</strong> is not a valid phone number.', 'byteplant-phone-validator' ), 'error' );
			}
		}
		return $phone;
	}

	/**
	 * Set the validator.
	 *
	 * @param object $validator The validator.
	 *
	 * @return object
	 */
	public function set_validator( $validator ) {

		$this->validator = (object) $validator;
		return $this->get_validator();
	}

	/**
	 * Get the validator.
	 *
	 * @return object
	 */
	public function get_validator() {

		return $this->validator;
	}
}
