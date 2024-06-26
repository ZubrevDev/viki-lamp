<?php if (!defined('ABSPATH')) {
    exit;
} ?>
<div class="dhlpwc-shipping-method-parcelshop-option"
    <?php echo !empty($postal_code) ? 'data-search-value="' . esc_attr($postal_code) . '"' : '' ?>
    <?php echo !empty($country_code) ? 'data-country-code="' . esc_attr($country_code) . '"' : '' ?>
>
    <?php if (!empty($parcelshop)) : ?>
        <span class="dhlpwc-parcelshop-option-message dhlpwc_notice"><?php echo esc_html($parcelshop->name) ?></span><br/>
        <input type="button" class="dhlpwc-parcelshop-option-change" value="<?php _e('Change', 'dhlpwc') ?>"/>
    <?php else : ?>
        <span class="dhlpwc-parcelshop-option-message dhlpwc_warning"><?php _e('No location selected.', 'dhlpwc') ?></span>
        <input type="button" class="dhlpwc-parcelshop-option-change" value="<?php _e('Select', 'dhlpwc') ?>"/>
    <?php endif ?>
</div>
