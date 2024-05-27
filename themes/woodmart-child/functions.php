<?php
/**
 * Enqueue script and styles for child theme
 */
function woodmart_child_enqueue_styles() {
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'woodmart-style' ), woodmart_get_theme_info( 'Version' ) );
}
add_action( 'wp_enqueue_scripts', 'woodmart_child_enqueue_styles', 10010 );


function remove_home_from_breadcrumbs($defaults) {
    $defaults['home'] = '';
    return $defaults;
}
add_filter('woocommerce_breadcrumb_defaults', 'remove_home_from_breadcrumbs');

function custom_product_shortcode($atts) {
    $atts = shortcode_atts(
        array(
            'category' => '', 
            'large_count' => 1, 
            'small_count' => 3, 
        ),
        $atts,
        'custom_product_shortcode'
    );

    $category = get_term_by('slug', $atts['category'], 'product_cat');

    if ($category) {
        $child_categories = get_terms('product_cat', array('parent' => $category->term_id));
        if (!empty($child_categories)) {
            foreach ($child_categories as $child_category) {
				echo '<h2 class="category-title">' . $child_category->name . '</h2>';
                echo '<div class="container-collections">';
               
                
				echo do_shortcode('[products limit="' . $atts['large_count'] . '" columns="1" orderby="rand" category="' . $child_category->slug . '" class="large-prod-layout"]');
                
                for ($i = 1; $i <= 3; $i++) {
                    echo '<div class="smol-prod-layout-' . $i . '">';
					echo do_shortcode('[products limit="' . $atts['small_count'] . '" columns="1" orderby="rand" category="' . $child_category->slug . '" class=""]');
                    echo '</div>';
                }
                
                echo '<div class="smol-prod-layout-btn"><a href="' . get_category_link($child_category->term_id) . '" class="view-all-button">Discover more</a></div>';
                echo '</div>';
            }
        }
    }
}

add_shortcode('custom_product_shortcode', 'custom_product_shortcode');


add_theme_support( 'title-tag' );

add_action('user_register', 'save_extra_register_fields');

function save_extra_register_fields($user_id) {
    if (isset($_POST['billing_phone'])) {
        update_user_meta($user_id, 'billing_phone', $_POST['billing_phone']);
    }
}


add_action('woocommerce_register_form_start', 'add_phone_field_registration_form');
function add_phone_field_registration_form() {
    ?>
    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
        <label for="reg_billing_phone"><?php _e('Phone', 'woocommerce'); ?> <span class="required">*</span></label>
        <input type="text" class="input-text" name="billing_phone" id="reg_billing_phone" value="<?php if (!empty($_POST['billing_phone'])) echo esc_attr(wp_unslash($_POST['billing_phone'])); ?>" />
    </p>
    <?php
}
add_filter('woocommerce_registration_errors', 'validate_phone_field', 10, 3);
function validate_phone_field($errors, $username, $email) {
    if (empty($_POST['billing_phone'])) {
        $errors->add('billing_phone_error', __('Please enter your phone number.', 'woocommerce'));
    }
    return $errors;
}
add_action('woocommerce_created_customer', 'save_phone_field');
function save_phone_field($customer_id) {
    if (isset($_POST['billing_phone']) && !empty($_POST['billing_phone'])) {
        update_user_meta($customer_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']));
    }
}

add_action('woocommerce_created_customer', 'save_registration_password', 10, 1);
function save_registration_password($customer_id) {
    if (isset($_POST['reg_password'])) {
        wp_set_password($_POST['reg_password'], $customer_id);
    }
}

add_action('after_setup_theme', 'custom_add_image_sizes');
function custom_add_image_sizes() {
    add_image_size('custom_cart_thumb', 196, 196, true); 
}


