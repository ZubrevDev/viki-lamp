<?php
/**
 * Registers and process the validate phone request.
 *
 * @package BBPV\Ajax
 */

/**
 * Class BPPV_Ajax_Validate_Phone
 */
class BPPV_Ajax_Validate_Phone {

	/**
	 * The handler object.
	 *
	 * @var object
	 */
	protected $handler = NULL;

	/**
	 * Whether the ajax handler is private.
	 *
	 * @var bool
	 */
	protected $private = TRUE;

	/**
	 * The action.
	 *
	 * @var string
	 */
	protected $action = 'bppy-verify-phone';

	/**
	 * The validator object
	 *
	 * @var object
	 */
	protected $validator = NULL;

	/**
	 * The callback.
	 */
	public function callback() {

		$return = array(
			'is_valid' => NULL,
			'status'   => '',
			'error'    => TRUE,
		);

		if ( empty( $_POST['bppv-nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['bppv-nonce'] ) ), 'validate-phone' ) ) { // Input var okay.
			wp_send_json( $return );
		}

		if ( empty( $_POST['number'] ) ) { // Input var okay.
			wp_send_json( $return );
		}

		$phone = sanitize_text_field( wp_unslash( $_POST['number'] ) ); // Input var okay.
		$this->validator->set_phone( $phone );
		$is_valid = $this->validator->validate();
		$return = array(
			'is_valid' => $is_valid,
			'status'   => $this->validator->get_phone_status(),
			'error'    => FALSE,
		);

		wp_send_json( $return );
	}

	/**
	 * Register the ajax action.
	 */
	public function register() {

		$this->handler->register( $this->get_action(), array( $this, 'callback' ), $this->get_private() );
	}
	/**
	 * Set the handler.
	 *
	 * @param object $handler The handler.
	 *
	 * @return object
	 */
	public function set_handler( $handler ) {

		$this->handler = (object) $handler;
		return $this->get_handler();
	}

	/**
	 * Get the handler.
	 *
	 * @return object
	 */
	public function get_handler() {

		return $this->handler;
	}

	/**
	 * Set the action.
	 *
	 * @param string $action The action.
	 *
	 * @return string
	 */
	public function set_action( $action ) {

		$this->action = sanitize_key( (string) $action );
		return $this->get_action();
	}

	/**
	 * Get the action.
	 */
	public function get_action() {

		return $this->action;
	}

	/**
	 * Set the private.
	 *
	 * @param boolean $private The private boolean.
	 *
	 * @return boolean
	 */
	public function set_private( $private ) {

		$this->private = (bool) $private;
		return $this->get_private();
	}

	/**
	 * Get the private.
	 *
	 * @return boolenn
	 */
	public function get_private() {

		return $this->private;
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
