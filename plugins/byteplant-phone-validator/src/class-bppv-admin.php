<?php
/**
 * Registers and handles the admin area.
 *
 * @package BPPV
 */

/**
 * Class BBPV_Admin
 */
class BPPV_Admin {

	/**
	 * Plugin instance.
	 *
	 * @see   get_instance()
	 * @var  object
	 */
	protected static $instance = NULL;

	/**
	 * Load the admin interface
	 *
	 * @return void
	 */
	public function load() {

		// Check, if the admin notice should be hidden.
		if (
			current_user_can( 'manage_options' ) &&
			! empty( $_GET['bppv-api-key-notice'] ) && // Input var okay.
			wp_verify_nonce( sanitize_key( wp_unslash( $_GET['bppv-api-key-notice'] ) ), 'remove-api-key-notice' ) // Input var okay.
		) {
			update_option( 'bppv-api-key-invalid', 0 );
		}

		// Check, if the admin notice should be displayed.
		if ( 1 === (int) get_option( 'bppv-api-key-invalid' ) ) {
			add_action( 'admin_notices', array( $this, 'show_notice' ) );
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Shows the admin notice.
	 *
	 * @return void
	 */
	public function show_notice() {
		$url = admin_url( 'options-general.php' );
		$url = add_query_arg(
			array(
				'page'                => 'byteplant_phone_validator',
				'bppv-api-key-notice' => wp_create_nonce( 'remove-api-key-notice' ),
			),
			$url
		);
		?>
		<div class="notice notice-error">
			<p>
				<?php esc_html_e( 'The API Key is invalid.', 'byteplant-phone-validator' ); ?>
				<a href="<?php echo esc_url( $url ); ?>"><?php esc_html_e( 'Go to settings and hide message', 'byteplant-phone-validator' ); ?></a>
			</p>

		</div>
		<?php
	}

	/**
	 * Register the admin menu.
	 *
	 * @return void
	 */
	function admin_menu() {

		add_options_page( __('Byteplant Phone Validator', 'byteplant-phone-validator' ), __('Byteplant Phone Validator', 'byteplant-phone-validator' ), 'manage_options', 'byteplant_phone_validator', array( $this, 'render_page' ) );
	}

	/**
	 * Render the page.
	 *
	 * @return void
	 */
	function render_page() {

		?>
		<div class="wrap">
			<h1><?php esc_html_e('Byteplant Phone Validator', 'byteplant-phone-validator' ); ?></h1>
			<form action='options.php' method='post'>
				<?php
				settings_fields( 'byteplant_phone_validator' );
				do_settings_sections( 'byteplant_phone_validator' );
				submit_button();
				?>
			</form>

			<div class="card bppvp-card" style="">
				<h2><?php esc_html_e( 'Validate Phone Number', 'byteplant-phone-validator' ); ?></h2>
				<p><?php esc_html_e( 'Please enter the phone number you want to validate.', 'byteplant-phone-validator' ); ?></p>
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php esc_html_e( 'Phone number', 'byteplant-phone-validator' ); ?></th>
							<td>
								<input id="bppvp-phone" value="" type="text">
							</td>
						</tr>
					</tbody>
				</table>
				<p id="bppvp-message"></p>
				<p class="submit">
					<input name="submit" id="bppvp-button-validate" class="button button-primary" value="<?php echo esc_attr( __( 'Validate', 'byteplant-phone-validator' ) ); ?>" type="submit">
				</p>
				<ul id="bppvp-list"></ul>
			</div>
		</div>
		<?php
	}

	/**
	 * Enqueue the necessary scripts and styles
	 *
	 * @param string $hook The page hook.
	 *
	 * @return void
	 */
	function enqueue( $hook ) {

		if ( 'settings_page_byteplant_phone_validator' !== $hook ) {
			return;
		}

		$plugin = BPPV_Plugin::get_instance();
		$plugin_url = $plugin->get_plugin_url();
		$min = ( ! $plugin->is_debug() ) ? '.min' : '';
		wp_register_style( 'bppvp_main_style', $plugin_url . 'assets/css/bppvp_style' . $min . '.css' );
		wp_register_script('bppvp_main_script', $plugin_url . 'assets/js/bppvp_script' . $min . '.js', array( 'jquery', 'underscore' ), '1.0', TRUE );

		$js_vars = $plugin->js_localization();
		$js_vars['ul_tpl'] = '<% console.log( data ); %><li><span><%- data.status %></span><%- data.phone %></li>';

		wp_localize_script( 'bppvp_main_script', 'bppvp', $js_vars);
		wp_enqueue_script( 'bppvp_main_script' );
		wp_enqueue_style( 'bppvp_main_style' );
	}

	/**
	 * Register the settings
	 *
	 * @return void
	 */
	function register_settings() {

		register_setting( 'byteplant_phone_validator', 'bppvp_settings' );

		add_settings_section(
			'bppvp_pluginPage_section',
			__( 'Phone Validator Options', 'byteplant-phone-validator' ),
			array( $this, 'render_section' ),
			'byteplant_phone_validator'
		);

		add_settings_field(
			'bppvp_api_key',
			__( 'API Key', 'byteplant-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'apikey',
				'type'    => 'text',
				'key'     => 'bppvp_api_key',
				'default' => '',
			)
		);

		add_settings_field(
			'bppvp_auto_check',
			__( 'Validate phone numbers on WooCommerce Checkout', 'bpmvp-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'woocommerce',
				'type'    => 'checkbox',
				'key'     => 'bppvp_auto_check',
				'default' => 0,
			)
		);

		add_settings_field(
			'bppvp_cf7_check',
			__( 'Validate phone numbers with Contact Form 7', 'bpmvp-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'cf7',
				'type'    => 'checkbox',
				'key'     => 'bppvp_cf7_check',
				'default' => 0,
			)
		);

		add_settings_field(
			'bppvp_gravity_check',
			__( 'Validate phone numbers with Gravity Forms', 'bpmvp-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'gf',
				'type'    => 'checkbox',
				'key'     => 'bppvp_gravity_check',
				'default' => 0,
			)
		);

		add_settings_field(
			'bppvp_wpforms_check',
			__( 'Validate phone numbers with WPForms', 'bpmvp-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'wpf',
				'type'    => 'checkbox',
				'key'     => 'bppvp_wpforms_check',
				'default' => 0,
			)
		);

		add_settings_field(
			'bppvp_ninja_check',
			__( 'Validate phone numbers with Ninja Forms', 'bpmvp-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'nf',
				'type'    => 'checkbox',
				'key'     => 'bppvp_ninja_check',
				'default' => 0,
			)
		);

		add_settings_field(
			'bppvp_country',
			__( 'Country (leave empty to validate international phone numbers)', 'bpmvp-phone-validator' ),
			array( $this, 'render_settings_field' ),
			'byteplant_phone_validator',
			'bppvp_pluginPage_section',
			array(
				'id'      => 'country',
				'type'    => 'select',
				'key'     => 'bppvp_country',
				'default' => 0,
			)
		);
	}

	/**
	 * Renders the section area.
	 *
	 * @return void
	 */
	function render_section() {

		$plugin = BPPV_Plugin::get_instance();

		echo '<p>';
		echo wp_kses_post( __('International Phone Number Validation<br/>
The Phone-Validator real-time validation API provides detailed information for each phone number:<br/>
Status: VALID/CONFIRMED, VALID/UNCONFIRMED or INVALID<br/>
Line type: FIXED_LINE, MOBILE, VOIP etc.<br/>
Geolocation: Region and city<br/>
Phone number correction and re-formatting according to national and international standards.<br/>
During the entire process, the phone number is not contacted in any way, nor will any phones connected to a line ring.', 'byteplant-phone-validator' ) );
		echo '</p>';

		echo '<br><br>';
		echo wp_kses_post( __( 'You can register for a <a href="https://www.phone-validator.net/api.html" target="_blank">free API key</a> (limited to 100 phone number checks).<br>
If you want to verify more than 100 numbers, please have a look at our pay-as-you-go pricing model and the <a href="https://www.phone-validator.net/pricing.html" target="_blank">subscription plans</a> we offer.', 'byteplant-phone-validator' ) );
	}

	/**
	 * Render a settings field
	 *
	 * @param array $args Specify the field.
	 *
	 * @return void
	 */
	public function render_settings_field( $args ) {
		$default_args = array(
			'id'      => 'apikey',
			'type'    => 'text',
			'key'     => 'bppvp_api_key',
			'default' => 0,
		);
		$args = wp_parse_args( $args, $default_args );

		$plugin = BPPV_Plugin::get_instance();
		$option = $plugin->get_option( $args['key'] );
		$value = ( NULL !== $option ) ? $option : $args['default'];

		if ( 'text' === $args['type'] ) :
		?>
		<input
			id="bppv_phone_<?php echo esc_attr( $key ); ?>"
			type="text"
			name="bppvp_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="<?php echo esc_attr( $value ); ?>"
		>
		<?php
		elseif ( 'checkbox' === $args['type'] ) : ?>
		<input
			id="bppv_phone_<?php echo esc_attr( $key ); ?>"
			type="checkbox"
			name="bppvp_settings[<?php echo esc_attr( $args['key'] ); ?>]"
			value="1"
			<?php checked( $value, 1 ); ?>
		>
		<?php
		elseif ( 'select' === $args['type'] ) : ?>
		<select id="bppv_phone_<?php echo esc_attr( $key ); ?>" name="bppvp_settings[<?php echo esc_attr( $args['key'] ); ?>]">
			<option></option>
			<?php
			$countries = BPPV_Countries::get_list();
			foreach ( $countries as $code => $name ) :
			?>
			<option <?php selected( $value, $code ); ?> value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $name ); ?></option>
			<?php
			endforeach;
			?>
		</select>
		<?php
		endif;

	}

	/**
	 * Access this plugin’s working instance
	 *
	 * @wp-hook plugins_loaded
	 * @return  object of this class
	 */
	public static function get_instance() {

		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}


	/**
	 * Empty and protected constructor.
	 */
	protected function __construct() {}

	/**
	 * Empty and private clone.
	 */
	private function __clone(){}

}
