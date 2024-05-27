<?php
/**
 * The main API
 *
 * @package BBPV/API
 */

/**
 * Class BPPV_API
 */
class BPPV_API {

	/**
	 * The API endpoint
	 *
	 * @var string
	 */
	protected $endpoint = 'https://api.phone-validator.net/api/v2/verify';

	/**
	 * The phone number to validate.
	 *
	 * @var string
	 */
	protected $phone = NULL;

	/**
	 * The API Key.
	 *
	 * @var string
	 */
	protected $apikey = NULL;

	/**
	 * The response object.
	 *
	 * @var object
	 */
	protected $response = NULL;

	/**
	 * The country code.
	 *
	 * @var string
	 */
	protected $country_code = '';

	/**
	 * Perform the request.
	 *
	 * @return null|object
	 */
	public function request( $country_code = '' ) {

		$response = wp_cache_get( $this->get_phone(), 'bppv' );
		if ( $response ) {
			return $this->set_response( $response );
		}

		$args = array(
			'method'   => 'POST',
			'timeout'  => 45,
			'blocking' => TRUE,
		    'body'     => array(
				'PhoneNumber' => $this->get_phone(),
				'APIKey'      => $this->get_apikey(),
				'PluginVersion' => BPPV_PLUGIN_CURRENT_VERSION
			)
		);

		if ( ! empty( $this->get_country_code() ) ) {
			$args['body']['CountryCode'] = $this->get_country_code();
		}
		if ( ! empty( $country_code ) ) {
			$args['body']['CountryCode'] = $country_code;
		}

		$result = wp_remote_post( $this->endpoint, $args );

		if ( ! is_wp_error( $result ) ) {
			$response = $this->set_response( json_decode( wp_remote_retrieve_body( $result ) ) );
			wp_cache_set( $this->get_phone(), $response, 'bppv', 86400); // cache 24h
			return $response;
		}
		return NULL;
	}

	/**
	 * Get the endpoint.
	 *
	 * @return string
	 */
	public function get_endpoint() {

		return $this->endpoint;
	}

	/**
	 * Set the endpoint.
	 *
	 * @param string $endpoint The endpoint.
	 *
	 * @return string
	 */
	public function set_endpoint( $endpoint ) {

		$this->endpoint = (string) $endpoint;
		return $this->get_endpoint();
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

		$phone = trim( (string) $phone );
		$this->phone = preg_replace( '/^00/', '+', $phone );
		return $this->get_phone();
	}

	/**
	 * Get the country code.
	 *
	 * @return string
	 */
	public function get_country_code() {

		return $this->country_code;
	}

	/**
	 * Set the country code.
	 *
	 * @param string $code The country code.
	 *
	 * @return string
	 */
	public function set_country_code( $code ) {

		$this->country_code = (string) $code;
		return $this->get_country_code();
	}

	/**
	 * Get the API Key.
	 *
	 * @return string
	 */
	public function get_apikey() {

		return $this->apikey;
	}

	/**
	 * Set the API Key.
	 *
	 * @param string $apikey The API Key.
	 *
	 * @return string
	 */
	public function set_apikey( $apikey ) {

		$this->apikey = (string) $apikey;
		return $this->get_apikey();
	}

	/**
	 * Get the Response Object.
	 *
	 * @return object
	 */
	public function get_response() {

		return $this->response;
	}

	/**
	 * Set the Response Object.
	 *
	 * @param  object $response The Response Object.
	 *
	 * @return object
	 */
	public function set_response( $response ) {

		$this->response = (object) $response;
		return $this->get_response();
	}
}
