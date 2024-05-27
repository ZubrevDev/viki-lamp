<?php
/**
 * Handles the communication with Gravity Forms
 *
 * @package BBPV
 */

/**
 * Class BBPV_GravityForms
 */
class BPPV_GravityForms {

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
	 * constructor.
	 */
	public function __construct() {}

	/**
	 * Set up the handler.
	 */
	public function setup() {

		 add_filter( 'gform_field_validation', array( $this, 'validate' ), 10, 4 );
	}

	/**
	 * Validate the number.
	 *
	 * @param $result
	 * @param $value phone number to validate
	 * @param $form 
	 * @param $field
	 *
	 * @return $result
	 */
	public function validate( $result, $value, $form, $field ) {

    	if ( $field->type == 'phone' ) {
    		$this->validator->set_phone( $value );
    		$this->validator->validate();
    		if ( ! $this->validator->get_is_valid() ) {
        		$result['is_valid'] = false;
        		$result['message']  = __( 'Please enter a valid phone number.', 'byteplant-phone-validator' );
        	}
    	} 
    	return $result;
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
