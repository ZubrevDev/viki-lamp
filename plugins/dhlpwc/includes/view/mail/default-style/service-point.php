<?php

/**
 * @var string                        $label
 * @var DHLPWC_Model_API_Data_Address $address
 */

if (!defined('ABSPATH')) { exit; }

?>
<table cellspacing="0" cellpadding="0" border="0" style="width: 100%; vertical-align: top; margin-bottom: 40px; padding:0;">
    <tr>
        <td style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif; border:0; padding:0;">
            <h2><?php echo esc_html($label) ?></h2>
            <address class="address">
                <?php echo esc_html($name) ?><br/>
                <?php echo esc_html($address->street) ?> <?php echo esc_html($address->number) ?><br/>
                <?php echo esc_html($address->postal_code) ?> <?php echo esc_html($address->city) ?> <?php echo esc_html($address->country_code) ?>
            </address>
        </td>
    </tr>
</table>
