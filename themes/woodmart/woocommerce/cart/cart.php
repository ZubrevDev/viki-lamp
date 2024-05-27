<?php

/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 7.9.0
 */

defined('ABSPATH') || exit;

$update_cart_btn_classes = '';

if (function_exists('wc_wp_theme_get_element_class_name')) {
	$update_cart_btn_classes .= ' ' . wc_wp_theme_get_element_class_name('button');
}

if (woodmart_get_opt('update_cart_quantity_change')) {
	$update_cart_btn_classes .= ' wd-hide';
}
?>


<div class="woocommerce cart-content-wrapper">

	<?php do_action('woocommerce_before_cart'); ?>

	<form class="woocommerce-cart-form cart-data-form" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
		<div class="cart-table-section">

			<?php do_action('woocommerce_before_cart_table'); ?>

			<table class=" viki-cart-style shop_table shop_table_responsive cart woocommerce-cart-form__contents" cellspacing="0">
				<thead>
					<tr class="teble-heading">
						<th class="product-name"><?php esc_html_e('Item', 'woocommerce'); ?></th>
						<th class="product-description"><?php esc_html_e('', 'woocommerce'); ?></th>
						<th class="product-size"><?php esc_html_e('Size', 'woocommerce'); ?></th>
						<th class="product-quantity"><?php esc_html_e('Quantity', 'woocommerce'); ?></th>
						<th colspan="2" class="product-subtotal"><?php esc_html_e('Item total', 'woocommerce'); ?></th>
					</tr>

				</thead>
				<tbody>
					<?php do_action('woocommerce_before_cart_contents'); ?>

					<?php
					foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
						$_product   = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
						$product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

						if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_cart_item_visible', true, $cart_item, $cart_item_key)) {
							$product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
							$product_name      = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
					?>

							<tr class="woocommerce-cart-form__cart-item <?php echo esc_attr(apply_filters('woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key)); ?>">

								<td class="product-thumbnail image-size-styling">
									<?php
									if (!$product_permalink) {
										echo apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('custom_cart_thumb'), $cart_item, $cart_item_key);
									} else {
										printf('<a href="%s">%s</a>', esc_url($product_permalink), apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('custom_cart_thumb'), $cart_item, $cart_item_key));
									}
									?>
								</td>

								<td class="product-name" data-title="<?php esc_attr_e('Product', 'woocommerce'); ?>">
    <?php
    if ($_product->is_type('variation') && $_product->get_parent_id()) {
        $parent_product = wc_get_product($_product->get_parent_id());
        $product_name = apply_filters('woocommerce_cart_item_name', $parent_product->get_name(), $cart_item, $cart_item_key);
    } else {
        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
    }

    if (!$product_permalink) {
        echo wp_kses_post('<div class="product-name-text">' . $product_name . '</div>');
    } else {
        echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s"><div class="product-name-text">%s</div></a>', esc_url($product_permalink), $product_name), $cart_item, $cart_item_key));
    }

    if (!empty($cart_item['variation_id']) && is_array($cart_item['variation'])) {
        foreach ($cart_item['variation'] as $name => $value) {
            // Exclude "size" variation
            if ($name !== 'attribute_pa_size') {
                $attribute_name = wc_attribute_label(str_replace('attribute_', '', $name));
				echo '<div class="product-attribute"><span class="viki-value" >' . esc_html($attribute_name) . ':</span> ' . '<span class="viki-current-value"> ' . esc_html($value).'</span> ' . '</div>';
            }
        }
    }

	echo '<div class="product-attribute"><span class="viki-value" >Price:</span> ' . '<span class="viki-current-value"> ' . WC()->cart->get_product_price($_product) . '</span> ' . '</div>';

    do_action('woocommerce_after_cart_item_name', $cart_item, $cart_item_key);
    ?>
</td>

								<?php if (woodmart_get_opt('show_sku_in_cart')) : ?>
									<td class="product-sku" data-title="<?php esc_attr_e('SKU', 'woocommerce'); ?>">
										<?php if ($_product->get_sku()) : ?>
											<?php echo esc_html($_product->get_sku()); ?>
										<?php else : ?>
											<?php esc_html_e('N/A', 'woocommerce'); ?>
										<?php endif; ?>
									</td>
								<?php endif; ?>

								<td class="product-size" data-title="<?php esc_attr_e('Size', 'woocommerce'); ?>">
									<div class="size-style">
										<?php
										if (!empty($cart_item['variation']['attribute_pa_size'])) {
											echo esc_html($cart_item['variation']['attribute_pa_size']);
										} else {
											echo esc_html__('', 'woocommerce');
										}
										?>
									</div>
								</td>

								<td class="product-quantity" data-title="<?php esc_attr_e('Quantity', 'woocommerce'); ?>">
									<?php
									if ($_product->is_sold_individually()) {
										$min_quantity = 1;
										$max_quantity = 1;
									} else {
										$min_quantity = 0;
										$max_quantity = $_product->get_max_purchase_quantity();
									}

									$product_quantity = woocommerce_quantity_input(
										array(
											'input_name'   => "cart[{$cart_item_key}][qty]",
											'input_value'  => $cart_item['quantity'],
											'max_value'    => $max_quantity,
											'min_value'    => $min_quantity,
											'product_name' => $product_name,
										),
										$_product,
										false
									);

									echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
									?>
								</td>

								<td class="product-subtotal" data-title="<?php esc_attr_e('Subtotal', 'woocommerce'); ?>">
									<?php
									echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
									?>
								</td>
							</tr>
							<tr class="">
								
								<td class="product-remove" colspan="6">

									<a href="#" data-group-id="save" class="add-to-wish-list-icon" data-added-text="<?php esc_html_e('Add to wishlist', 'woodmart'); ?>" data-create-text="<?php esc_html_e('Create wishlist', 'woodmart'); ?>" data-move-text="<?php esc_html_e('Move to wishlist', 'woodmart'); ?>">
										<span class="product-remove-text"><?php esc_html_e('Add to wishlist', 'woodmart'); ?></span>
									</a>
									</a>
									<?php
									echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										'woocommerce_cart_item_remove_link',
										sprintf(
											'<a href="%s" class="add-image-to-remove" aria-label="%s" data-product_id="%s" data-product_sku="%s"><span class="product-remove-text">Remove</span> </a>',
											esc_url(wc_get_cart_remove_url($cart_item_key)),
											/* translators: %s is the product name */
											esc_attr(sprintf(__('Remove %s from cart', 'woocommerce'), wp_strip_all_tags($product_name))),
											esc_attr($product_id),
											esc_attr($_product->get_sku())
										),
										$cart_item_key
									);
									?>
								</td>
							</tr>

					<?php
						}
					}
					?>

					<?php do_action('woocommerce_cart_contents'); ?>

					<tr class="wd-cart-action-row">
						<td colspan="12" class="actions">
							<div class="cart-actions">
								<?php if (wc_coupons_enabled()) { ?>
									<div class="coupon wd-coupon-form">
										<label for="coupon_code" class="screen-reader-text">
											<?php esc_html_e('Coupon:', 'woocommerce'); ?>
										</label>
										<input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e('Coupon code', 'woocommerce'); ?>" />
										<button type="submit" class="button<?php echo esc_attr(function_exists('wc_wp_theme_get_element_class_name') && wc_wp_theme_get_element_class_name('button') ? ' ' . wc_wp_theme_get_element_class_name('button') : ''); ?>" name="apply_coupon" value="<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>">
											<?php esc_attr_e('Apply coupon', 'woocommerce'); ?>
										</button>
										<?php do_action('woocommerce_cart_coupon'); ?>
									</div>
								<?php } ?>

								<button type="submit" class="button<?php echo esc_attr($update_cart_btn_classes); ?>" name="update_cart" value="<?php esc_attr_e('Update cart', 'woocommerce'); ?>">
									<?php esc_html_e('Update cart', 'woocommerce'); ?>
								</button>

								<?php do_action('woocommerce_cart_actions'); ?>

								<?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
							</div>
						</td>
					</tr>

					<?php do_action('woocommerce_after_cart_contents'); ?>
				</tbody>
			</table>

			<?php do_action('woocommerce_after_cart_table'); ?>
		</div>
	</form>

	<?php do_action('woocommerce_before_cart_collaterals'); ?>
	<div class="cart-totals-section cart-collaterals">
		<?php woocommerce_cart_totals(); ?>
	</div>

	<div class="cart-collaterals">

		<?php
		/**
		 * Cart collaterals hook.
		 *
		 * @hooked woocommerce_cross_sell_display
		 * @hooked woocommerce_cart_totals - 10
		 */
		do_action('woocommerce_cart_collaterals');
		?>

	</div>

	<?php do_action('woocommerce_after_cart'); ?>
</div>