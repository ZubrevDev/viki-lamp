<?php
/**
 * The main ajax handler.
 *
 * @package BBPV\Ajax
 */

/**
 * Class BPPV_Ajax_Handler
 */
class BPPV_Ajax_Handler {

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
	protected $action = '';

	/**
	 * The callback funcition.
	 *
	 * @var callable
	 */
	protected $callback = NULL;

	/**
	 * Registers a new handler.
	 *
	 * @param string   $action  The action to register.
	 * @param callable $callback The callback function.
	 * @param boolean  $private If it is public or not (Default: TRUE).
	 */
	public function register( $action, $callback, $private = TRUE ) {

		$this->set_action( $action );
		$this->set_callback( $callback );
		$this->set_private( $private );

		add_action( 'wp_ajax_' . $this->get_action(), array( $this, 'do_callback' ) );

		if ( ! $this->get_private() ) {
			add_action( 'wp_ajax_nopriv_' . $this->get_action(), array( $this, 'do_callback' ) );
		}
	}

	/**
	 * The callback function
	 */
	public function do_callback() {
		call_user_func( $this->callback );
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
	 * Set the callback.
	 *
	 * @param callable $callback The callback.
	 *
	 * @return bool|callable
	 */
	public function set_callback( $callback ) {

		if ( ! is_callable( $callback ) ) {
			return FALSE;
		}
		$this->callback = $callback;
		return $this->get_callback();
	}

	/**
	 * Get the callback.
	 *
	 * @return callable
	 */
	public function get_callback() {

		return $this->callback;
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
}
