<?php if (!defined('ABSPATH')) { exit; } ?>
<textarea
        class="dhlpwc-option-data"
        rows="3"
        cols="20"
        type="textarea"
        placeholder="<?php echo esc_attr($placeholder) ?>"
><?php if (!empty($value)) : echo esc_attr($value); endif ?></textarea>
