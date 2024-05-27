<?php if (!defined('ABSPATH')) { exit; } ?>
<?php if (!empty($tracking_urls)) : ?>
    <h3><?php _e('Check shipment status', 'dhlpwc') ?></h3>
    <?php foreach ($tracking_urls as $tracking_code => $tracking_url) : ?>
        <div>
            <a href="<?php echo esc_url($tracking_url) ?>" target="_blank">
                <?php echo esc_html($tracking_code) ?>
            </a>
        </div>
    <?php endforeach ?>
    <p/>
<?php endif ?>
