<?php

function display_product_info_shortcode($atts) {

	extract(shortcode_atts(array(
        'id' => 0 
    ), $atts));

    if ($id) {
        $post = get_post($id);

        // Проверка, что пост существует и является типом "Товары" (при необходимости, замените на свой тип записи)
        if ($post && $post->post_type === 'product') {
            $product_title = get_the_title($post->ID);
            $product_price = get_post_meta($post->ID, '_regular_price', true);
            $product_image = get_the_post_thumbnail($post->ID, 'full');
            $product_link = get_permalink($post->ID);
            
            $output = '<div class="product-info">';
            $output .= '<a href="' . esc_url($product_link) . '"><div class="product-image">' . $product_image . '</div></a>';
            $output .= '<h2><a href="' . esc_url($product_link) . '">' . ($product_title) . '</a></h2>';
			$currency_symbol = get_woocommerce_currency_symbol();
			$output .= '<div class="product-price">' . esc_html($currency_symbol) . ' ' . esc_html($product_price) . '</div>';
						$output .= '</div>';

            return $output;
        }
    }

    return 'Товар не найден.';
}

add_shortcode('product_info', 'display_product_info_shortcode');

           
/**
 *
 * The framework's functions and definitions
 */

define( 'WOODMART_THEME_DIR', get_template_directory_uri() );
define( 'WOODMART_THEMEROOT', get_template_directory() );
define( 'WOODMART_IMAGES', WOODMART_THEME_DIR . '/images' );
define( 'WOODMART_SCRIPTS', WOODMART_THEME_DIR . '/js' );
define( 'WOODMART_STYLES', WOODMART_THEME_DIR . '/css' );
define( 'WOODMART_FRAMEWORK', '/inc' );
define( 'WOODMART_DUMMY', WOODMART_THEME_DIR . '/inc/dummy-content' );
define( 'WOODMART_CLASSES', WOODMART_THEMEROOT . '/inc/classes' );
define( 'WOODMART_CONFIGS', WOODMART_THEMEROOT . '/inc/configs' );
define( 'WOODMART_HEADER_BUILDER', WOODMART_THEME_DIR . '/inc/header-builder' );
define( 'WOODMART_ASSETS', WOODMART_THEME_DIR . '/inc/admin/assets' );
define( 'WOODMART_ASSETS_IMAGES', WOODMART_ASSETS . '/images' );
define( 'WOODMART_API_URL', 'https://xtemos.com/licenses/api/' );
define( 'WOODMART_DEMO_URL', 'https://woodmart.xtemos.com/' );
define( 'WOODMART_PLUGINS_URL', WOODMART_DEMO_URL . 'plugins/' );
define( 'WOODMART_DUMMY_URL', WOODMART_DEMO_URL . 'dummy-content-new/' );
define( 'WOODMART_TOOLTIP_URL', WOODMART_DEMO_URL . 'theme-settings-tooltips/' );
define( 'WOODMART_SLUG', 'woodmart' );
define( 'WOODMART_CORE_VERSION', '1.0.40' );
define( 'WOODMART_WPB_CSS_VERSION', '1.0.2' );

if ( ! function_exists( 'woodmart_load_classes' ) ) {
	function woodmart_load_classes() {
		$classes = array(
			'Singleton.php',
			'Api.php',
			'Googlefonts.php',
			'Config.php',
			'Layout.php',
			'License.php',
			'Notices.php',
			'Options.php',
			'Stylesstorage.php',
			'Theme.php',
			'Themesettingscss.php',
			'Vctemplates.php',
			'Wpbcssgenerator.php',
			'Registry.php',
			'Pagecssfiles.php',
		);

		foreach ( $classes as $class ) {
			require WOODMART_CLASSES . DIRECTORY_SEPARATOR . $class;
		}
	}
}

woodmart_load_classes();

new WOODMART_Theme();

define( 'WOODMART_VERSION', woodmart_get_theme_info( 'Version' ) );

function add_custom_css() {
    wp_enqueue_style('custom-css', get_template_directory_uri() . '/css/viki-theme-style.css');
}
add_action('wp_enqueue_scripts', 'add_custom_css');


