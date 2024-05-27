<?php
/**
 * Handles the communication with the Contact Form 7 plugin.
 *
 * @package BBPV
 */

/**
 * Class BBPV_ContactForm7
 */
class BPPV_ContactForm7 {

	/**
	 * The validator object
	 *
	 * @var object
	 */
	protected $validator = NULL;

	/**
	 * Whether to display an error or not
	 *
	 * @var bool
	 */
	protected $display_error = FALSE;


	/**
	 * BPPV_WC constructor.
	 */
	public function __construct() {}

	/**
	 * Set up the handler.
	 */
	public function setup() {

		 add_filter( 'wpcf7_is_tel', array( $this, 'validate' ), 10, 2 );
	}

	/**
	 * Validate the number.
	 *
	 * @param boolean $is_valid validation status
	 * @param string  $phone    number to validate
	 *
	 * @return bool
	 */
	public function validate( $is_valid, $phone ) {

		if ( ! $is_valid ) {
			return FALSE;
		}
		$this->validator->set_phone( $phone );
		return $this->validator->validate();
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
