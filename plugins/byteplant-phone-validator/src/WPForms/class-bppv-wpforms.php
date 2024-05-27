<?php
/**
 * Handles the communication with WPForms
 *
 * @package BBPV
 */

/**
 * Class BBPV_WPForms
 */
class BPPV_WPForms {

	/**
	 * The validator object
	 *
	 * @var object
	 */
	protected $validator = NULL;

	/**
	 * constructor.
	 */
	public function __construct() {}

	/**
	 * Set up the handler.
	 */
	public function setup() {

		add_filter( 'wpforms_process_after_filter', array( $this, 'validate' ), 10, 3 );
	}

	/**
	 * Validate form data.
	 *
    * @param  array  $fields    Sanitized entry fields. values/properties.
    * @param  array  $entry     Original $_POST global.
    * @param  array  $form_data Form data and settings.
	 */
	public function validate( $fields, $entry, $form_data ) {    

		foreach ( $fields as $field_id => $field ) {
			if ( !empty( $field['value'] ) && isset( $field['type'] ) && 'phone' == $field['type'] ) {
				$form_id = $form_data['id'];
				$phone = sanitize_text_field( $field['value'] );
    			$this->validator->set_phone( $phone );
    			$this->validator->validate();
				if ( !$this->validator->get_is_valid() ) {
        				wpforms()->process->errors[ $form_id ][ $field_id ] = esc_html__( 'Please enter a valid phone number.', 'byteplant-phone-validator' );
 				}
         }
      }  
      return $fields;
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