<?php
/**
 * Handles the communication with Ninja Forms
 *
 * @package BBPV
 */

/**
 * Class BBPV_NinjaForms
 */
class BPPV_NinjaForms {

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

		 add_filter( 'ninja_forms_submit_data', array( $this, 'validate'), 10, 1 );
	}

	/**
	 * Validate form data.
	 *
	 * @param array  form_data
	 *
	 * @return array form_data 
	 */
	public function validate( $form_data ) {

		foreach( $form_data[ 'fields' ] as $key => $field ) {
			if( stripos($field['key'], 'phone' ) === false ) continue;
			$value = $field['value'];
    		$this->validator->set_phone( $value );
    		$this->validator->validate();
		   if ( ! $this->validator->get_is_valid() ) {
				$field_id = $field['id'];
		    	$form_data['errors']['fields'][$field_id] = __( 'Please enter a valid phone number.', 'byteplant-phone-validator' );
		   }		    		
		}
		return $form_data;			
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
