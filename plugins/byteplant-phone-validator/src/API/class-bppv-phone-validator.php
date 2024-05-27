<?php
/**
 * The phone validator
 *
 * @package BPPV/API
 */

/**
 * Class BPPV_Phone_Validator
 */
class BPPV_Phone_Validator {

	/**
	 * Whether the number is valid.
	 *
	 * @var bool
	 */
	protected $is_valid = TRUE;

	/**
	 * The phone number to validate.
	 *
	 * @var string
	 */
	protected $phone = '';


	/**
	 * The phone number status.
	 *
	 * @var string
	 */
	protected $phone_status = '';

	/**
	 * The API object.
	 *
	 * @var object
	 */
	protected $api = NULL;

	/**
	 * Validate a phone number.
	 *
	 * @return boolean
	 */
	public function validate( $country_code = '' ) {

		$api = $this->get_api();
		$api->set_phone( $this->get_phone() );
		$result = $api->request( $country_code );
		if ( ! empty( $result->status ) ) {
			$this->set_phone_status( $result->status );
		}

		$response = $api->get_response();

		$this->set_is_valid( TRUE );

		// If there was an connection error, the phone number is valid.
		if ( NULL === $response ) {
			$this->set_is_valid( TRUE );

			/**
			 * Filters whether the phone number is valid.
			 *
			 * @param boolean $is_valid Whether the number is valid or not.
			 * @param object  $response The response
			 */
			return apply_filters( 'bppv_phone_valid', $this->get_is_valid(), $response );
		}

		$valid_statuse = array(
			'VALID_CONFIRMED',
			'VALID_UNCONFIRMED',
			'DELAYED',
			'API_KEY_INVALID_OR_DEPLETED',
			'RATE_LIMIT_EXCEEDED'
		);
		if ( ! in_array( $this->get_phone_status(), $valid_statuse, TRUE ) ) {
			$this->set_is_valid( FALSE );
		}

		// If the API Key is invalid or depleted, we update an option to show an admin notice.
		if ( 'API_KEY_INVALID_OR_DEPLETED' === $this->get_phone_status() ) {
			update_option( 'bppv-api-key-invalid', 1 );
		}

		/**
		 * Filters whether the phone number is valid.
		 *
		 * @param boolean $is_valid Whether the number is valid or not.
		 * @param object  $response The response
		 */
		return apply_filters( 'bppv_phone_valid', $this->get_is_valid(), $response );
	}

	/**
	 * Get the validity of a number.
	 *
	 * @return bool
	 */
	public function get_is_valid() {

		return $this->is_valid;
	}

	/**
	 * Set the validity of a number.
	 *
	 * @param bool $is_valid The validity.
	 *
	 * @return bool
	 */
	protected function set_is_valid( $is_valid ) {

		$this->is_valid = (bool) $is_valid;
		return $this->get_is_valid();
	}

	/**
	 * Get the phone number.
	 *
	 * @return string
	 */
	public function get_phone() {

		return $this->phone;
	}

	/**
	 * Set the phone number.
	 *
	 * @param string $phone The phone number.
	 *
	 * @return string
	 */
	public function set_phone( $phone ) {

		$this->phone = (string) $phone;
		return $this->get_phone();
	}

	/**
	 * Set the phone status.
	 *
	 * @param string $status The status.
	 *
	 * @return string
	 */
	public function set_phone_status( $status ) {

		$this->phone_status = (string) $status;
		return $this->get_phone_status();
	}

	/**
	 * Get the phone status.
	 *
	 * @return string
	 */
	public function get_phone_status() {

		return $this->phone_status;
	}

	/**
	 * Get the API
	 *
	 * @return object
	 */
	public function get_api() {

		return $this->api;
	}

	/**
	 * Set the API.
	 *
	 * @param object $api The API Object.
	 *
	 * @return object
	 */
	public function set_api( $api ) {

		$this->api = (object) $api;
		return $this->get_api();
	}
}
