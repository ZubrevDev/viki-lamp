<?php if (!defined('ABSPATH')) { exit; } ?>
<div class="dhlpwc-modal" <?php isset($modal_id) ? 'id="'.esc_attr($modal_id).'"' : '';?>>

    <div class="dhlpwc-modal-content">
        <div class="dhlpwc-modal-close-wrapper">
            <?php if (isset($logo)) : ?>
                <div class="dhlpwc-modal-logo">
                    <img src="<?php echo esc_attr($logo) ?>" />
                </div>
            <?php endif; ?>
            <span class="dhlpwc-modal-close">&times;</span>
        </div>
        <?php echo dhlpwc_esc_template($content) ?>
        <div class="clear"></div>
    </div>

</div>
