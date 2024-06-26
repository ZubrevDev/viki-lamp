<?php

/**
 * @package Duplicator
 */

use Duplicator\Utils\Help\Help;
use Duplicator\Libs\Snap\SnapJson;

defined("ABSPATH") || exit;

/**
 * Variables
 *
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array<string, mixed> $tplData
 */

$helpPageUrl = SnapJson::jsonEncode(Help::getHelpPageUrl());
?>
<script>
    jQuery(document).ready(function ($) {
        $('.duplicator-pro-help-open').click(function () { 
            if (Duplicator.Help.isDataLoaded()) {
                Duplicator.Help.Display();
            } else {
                Duplicator.Help.Load('<?php echo esc_url_raw($helpPageUrl); ?>');
            }
        });
    });
</script>
<div id="dup-meta-screen"></div>
<div class="dup-header">
    <img src="<?php echo esc_url(DUPLICATOR_PRO_PLUGIN_URL . 'assets/img/duplicator-header-logo.svg'); ?>" alt="Duplicator Logo" >
    <button class="duplicator-pro-help-open">
        <i class="fa-regular fa-question-circle"></i>
    </button>
</div>
