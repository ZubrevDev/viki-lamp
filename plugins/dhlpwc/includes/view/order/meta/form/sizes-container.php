<?php if (!defined('ABSPATH')) { exit; } ?>
<br/>
<?php echo dhlpwc_esc_template($header) ?>

<div class="dhlpwc-chosen-sizes">
    <div class="dhlpwc-chosen-size-template">
        <div class="dhlpwc-label-size-header">
            <?php echo esc_html(__('Package', 'dhlpwc')) ?> #<span class="dhlpwc-parcel-counter"></span>
        </div>
        <div class="dhlpwc-chosen-size-selection">
            <div class="dhlpwc-chosen-size-actions">
                <a href='#' class='button tips dhlpwc-label-action dhlpwc-label-edit-piece'><?php echo __('Edit') ?></a>
                <a href='#' class='button tips dhlpwc-label-action dhlpwc-label-delete-piece'><?php echo __('Delete') ?></a>
            </div>
            <div class="dhlpwc-chosen-size-info">
                <span class="dhlpwc-chosen-size">SIZE</span>
                ( <i></i> )
            </div>
        </div>
        <div class="dhlpwc-form-size-selections-edit"></div>
    </div>
</div>

<div class="dhlpwc-order-metabox-form-parceltypes">
    <div class="dhlpwc-label-size-header">
        <?php echo esc_html(__('Package', 'dhlpwc')) ?> #<span class="dhlpwc-parcel-counter"></span>
    </div>
    <div class="dhlpwc-form-content dhlpwc-form-sizes">
        <div class="dhlpwc-form-size-selections">
            <?php echo dhlpwc_esc_template($content) ?>
        </div>
    </div>
</div>
