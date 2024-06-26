<?php

use Duplicator\Addons\ProBase\License\License;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Models\BrandEntity;

 defined("ABSPATH") or die("");

$ui_css_installer = (DUP_PRO_UI_ViewState::getValue('dpro-pack-installer-panel') ? 'display:block' : 'display:none');

?>

<style>
    /*INSTALLER: Area */
    label.chk-labels {display:inline-block; margin-top:1px}
    table.dpro-install-tbl {width:98%;}
    table.dpro-install-tbl td{padding:4px}
    table.dpro-install-setup {width:100%}
    table.dpro-install-setup tr{vertical-align: top}
    div#dpro-pack-installer-panel div.tabs-panel{min-height:150px}
    div.dpro-panel-optional-txt {color:maroon}
    .disabled .dpro-panel-optional-txt:not(.maroon) {color:#777;}
    .maroon {color:maroon;}
</style>

<!-- ===================
INSTALLER -->
<div class="dup-box">
    <div class="dup-box-title">
        <i class="fa fa-bolt fa-sm"></i> <?php esc_html_e('Installer', 'duplicator-pro') ?>
        <button class="dup-box-arrow">
            <span class="screen-reader-text"><?php esc_html_e('Toggle panel:', 'duplicator-pro') ?> <?php esc_html_e('Installer Settings', 'duplicator-pro') ?></span>
        </button>
    </div>      
    <div class="dup-box-panel" id="dpro-pack-installer-panel" style="<?php echo esc_attr($ui_css_installer); ?>">
        <div class="dpro-panel-optional-txt">
            <b><?php esc_html_e('All values in this section are', 'duplicator-pro'); ?> <u><?php esc_html_e('optional', 'duplicator-pro'); ?></u></b>
            <i class="fas fa-question-circle fa-sm"
               data-tooltip-title="<?php esc_attr_e("Setup/Prefills", 'duplicator-pro'); ?>"
               data-tooltip="<?php
                esc_attr_e(
                    'All values in this section are OPTIONAL! If you know ahead of time the database input fields the installer will use, 
                    then you can optionally enter them here and they will be prefilled at install time.  
                    Otherwise you can just enter them in at install time and ignore all these options in the Installer section.',
                    'duplicator-pro'
                );
                ?>"></i>
        </div>

        <table class="dpro-install-setup" style="margin-top:-10px">
            <tr>
                <td colspan="2"><div class="dup-package-hdr-1"><?php esc_html_e("Setup", 'duplicator-pro') ?></div></td>
            </tr>
            <tr>
                <td style="width:130px"><b><?php esc_html_e("Branding", 'duplicator-pro') ?>:</b></td>
                <td>
                    <?php
                    if (License::can(License::CAPABILITY_BRAND)) :
                        $brands          = BrandEntity::getAllWithDefault();
                        $active_brand_id = DUP_PRO_Package_Template_Entity::get_manual_template()->installer_opts_brand;
                        if ($active_brand_id < 0) {
                            $active_brand_id = -1; // for old brand version
                        }
                        ?>
                        <select name="brand" id="brand">
                            <?php foreach ($brands as $i => $brand) { ?>
                                <option 
                                    value="<?php echo $brand->getId(); ?>" 
                                    title="<?php echo esc_attr($brand->notes); ?>" 
                                    <?php selected($brand->getId(), $active_brand_id); ?>
                                >
                                    <?php echo esc_html($brand->name); ?>
                                </option>
                            <?php } ?>
                        </select>
                        <?php
                        if ($active_brand_id > 0) {
                            $preview_url = ControllersManager::getMenuLink(
                                ControllersManager::SETTINGS_SUBMENU_SLUG,
                                SettingsPageController::L2_SLUG_PACKAGE,
                                SettingsPageController::L3_SLUG_PACKAGE_BRAND,
                                [
                                    'view'   => 'edit',
                                    'action' => 'edit',
                                    'id'     => intval($active_brand_id),
                                ]
                            );
                        } else {
                            $preview_url = ControllersManager::getMenuLink(
                                ControllersManager::SETTINGS_SUBMENU_SLUG,
                                SettingsPageController::L2_SLUG_PACKAGE,
                                SettingsPageController::L3_SLUG_PACKAGE_BRAND,
                                [
                                    'view'   => 'edit',
                                    'action' => 'default',
                                ]
                            );
                        }
                        ?>
                        <a href="<?php echo esc_url($preview_url); ?>" target="_blank" class="button" id="brand-preview">
                            <?php esc_html_e("Preview", 'duplicator-pro'); ?>
                        </a> &nbsp;
                        <i class="fas fa-question-circle fa-sm"
                           data-tooltip-title="<?php esc_attr_e("Choose Brand", 'duplicator-pro'); ?>"
                           data-tooltip="<?php esc_attr_e('This option changes the branding of the installer file. Click the preview button to see the selected style.', 'duplicator-pro'); ?>"
                        >
                        </i>
                    <?php else :
                        $link =  ControllersManager::getMenuLink(
                            ControllersManager::SETTINGS_SUBMENU_SLUG,
                            SettingsPageController::L2_SLUG_PACKAGE,
                            SettingsPageController::L3_SLUG_PACKAGE_BRAND
                        );
                        ?>
                        <a href="<?php echo esc_url($link); ?>"><?php esc_html_e("Enable Branding", 'duplicator-pro'); ?></a>
                    <?php endif; ?>
                    <br/><br/>
                </td>
            </tr>
        </table>
        <br/>

        <table style="width:100%">
            <tr>
                <td colspan="2"><div class="dup-package-hdr-1"><?php esc_html_e("Prefills", 'duplicator-pro') ?></div></td>
            </tr>
        </table>

        <!-- ===================
        BASIC/CPANEL TABS -->
        <div data-dpro-tabs="true">
            <ul>
                <li id="dpro-bsc-tab-lbl"><?php esc_html_e('Basic', 'duplicator-pro') ?></li>
                <li id="dpro-cpnl-tab-lbl"><?php esc_html_e('cPanel', 'duplicator-pro') ?></li>
            </ul>

            <!-- ===================
            TAB1: Basic -->
            <div>
                <div class="dup-package-hdr-2">
                    <?php esc_html_e("MySQL Server", 'duplicator-pro') ?>
                    <div class="dup-package-hdr-usecurrent">
                        <a href="javascript:void(0)" onclick="DupPro.Pack.ApplyDataCurrent('s1-installer-dbbasic')">
                        [<?php esc_html_e('use current', 'duplicator-pro') ?>]
                        </a>
                    </div>
                </div>

                <table class="dpro-install-tbl" id="s1-installer-dbbasic">
                    <tr>
                        <td style="width:130px"><b><?php esc_html_e("Host", 'duplicator-pro') ?>:</b></td>
                        <td><input type="text" name="dbhost" id="dbhost" maxlength="200" placeholder="<?php esc_html_e("example: localhost (value is optional)", 'duplicator-pro') ?>" data-current="<?php echo DB_HOST ?>"/></td>
                    </tr>
                    <tr>
                        <td><b><?php esc_html_e("Database", 'duplicator-pro') ?>:</b></td>
                        <td><input type="text" name="dbname" id="dbname" maxlength="100" placeholder="<?php esc_attr_e("example: DatabaseName (value is optional)", 'duplicator-pro') ?>" data-current="<?php echo DB_NAME ?>" /></td>
                    </tr>
                    <tr>
                        <td><b><?php esc_html_e("User", 'duplicator-pro') ?>:</b></td>
                        <td><input type="text" name="dbuser" id="dbuser" maxlength="100" placeholder="<?php esc_attr_e("example: DatabaseUser (value is optional)", 'duplicator-pro') ?>" data-current="<?php echo DB_USER ?>"/></td>
                    </tr>
                </table>
            </div>

            <!-- ===================
            TAB2: cPanel -->
            <div>

                
                <table class="dpro-install-tbl">
                    <tr>
                        <td colspan="2"><div class="dup-package-hdr-2"><?php esc_html_e("cPanel Login", 'duplicator-pro') ?></div></td>
                    </tr>
                    <tr>
                        <td style="width:130px"><b><?php esc_html_e("Automation", 'duplicator-pro') ?>:</b></td>
                        <td>
                            <input type="checkbox" name="cpnl-enable" id="cpnl-enable" value="1" >
                            <label for="cpnl-enable"><?php esc_html_e('Auto Select cPanel', 'duplicator-pro') ?></label>
                            <i class="fas fa-question-circle fa-sm"
                               data-tooltip-title="<?php esc_attr_e('Auto Select cPanel', 'duplicator-pro'); ?>"
                               data-tooltip="<?php esc_attr_e('Enabling this options will automatically select the cPanel tab when step one of the installer is shown.', 'duplicator-pro'); ?>">
                            </i>
                        </td>
                    </tr>
                    <tr>
                        <td><b><?php esc_html_e("Host", 'duplicator-pro') ?>:</b></td>
                        <td><input type="text" name="cpnl-host" id="cpnl-host"  maxlength="200" placeholder="<?php esc_attr_e("example: cpanelHost (value is optional)", 'duplicator-pro') ?>"/></td>
                    </tr>
                    <tr>
                        <td><b><?php esc_html_e("User", 'duplicator-pro') ?>:</b></td>
                        <td><input type="text" name="cpnl-user" id="cpnl-user" maxlength="200" placeholder="<?php esc_attr_e("example: cpanelUser (value is optional)", 'duplicator-pro') ?>"/></td>
                    </tr>
                </table><br/>


                <div class="dup-package-hdr-2">
                    <?php esc_html_e("MySQL Server", 'duplicator-pro') ?>
                    <div class="dup-package-hdr-usecurrent">
                        <a href="javascript:void(0)" onclick="DupPro.Pack.ApplyDataCurrent('s1-installer-dbcpanel')">
                        [<?php esc_html_e('use current', 'duplicator-pro') ?>]
                        </a>
                    </div>
                </div>
                

                <table class="dpro-install-tbl" id="s1-installer-dbcpanel">
                    <tr>
                        <td style="width:130px"><b><?php esc_html_e("Action", 'duplicator-pro') ?>:</b></td>
                        <td>
                            <select name="cpnl-dbaction" id="cpnl-dbaction">
                                <option value=""><?php _e('Default', 'duplicator-pro'); ?></option>
                                <option value="create"><?php _e('Create A New Database', 'duplicator-pro'); ?></option>
                                <option value="empty"><?php _e('Connect and Delete Any Existing Data', 'duplicator-pro'); ?></option>
                                <option value="rename"><?php _e('Connect and Backup Any Existing Data', 'duplicator-pro'); ?></option>
                                <option value="manual"><?php _e('Skip Database Extraction', 'duplicator-pro'); ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td style="width:130px"><b><?php esc_html_e("Host", 'duplicator-pro') ?>:</b></td>
                        <td><input type="text" name="cpnl-dbhost" id="cpnl-dbhost" maxlength="200" placeholder="<?php esc_attr_e("example: localhost (value is optional)", 'duplicator-pro') ?>" data-current="<?php echo esc_html(DB_HOST); ?>"/></td>
                    </tr>
                    <tr>
                        <td><b><?php esc_html_e("Database", 'duplicator-pro') ?>:</b></td>
                        <td>
                            <input 
                                type="text" 
                                name="cpnl-dbname" 
                                id="cpnl-dbname" 
                                data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" 
                                maxlength="100" 
                                placeholder="<?php esc_attr_e("example: DatabaseName (value is optional)", 'duplicator-pro') ?>" 
                                data-current="<?php echo esc_html(DB_NAME); ?>"
                            >
                        </td>
                    </tr>
                    <tr>
                        <td><b><?php esc_html_e("User", 'duplicator-pro') ?>:</b></td>
                        <td>
                            <input 
                                type="text" 
                                name="cpnl-dbuser" 
                                id="cpnl-dbuser" 
                                data-parsley-pattern="/^[a-zA-Z0-9-_]+$/" 
                                maxlength="100" 
                                placeholder="<?php esc_attr_e("example: DatabaseUserName (value is optional)", 'duplicator-pro') ?>" 
                                data-current="<?php echo esc_html(DB_USER); ?>" 
                            >
                        </td>
                    </tr>
                </table>

            </div>
        </div><br/>

        <small><?php esc_html_e("Additional inputs can be entered at install time.", 'duplicator-pro') ?></small>
        <br/><br/>
    </div>      
</div><br/>

<script>
    (function ($) {
        DupPro.Pack.ApplyDataCurrent = function (id)
        {
            $('#' + id + ' input').each(function ()
            {
                var attr = $(this).attr('data-current');
                if (typeof attr !== typeof undefined && attr !== false) {
                    $(this).val($(this).attr('data-current'));
                }
            });
        };
    <?php if (License::can(License::CAPABILITY_BRAND)) : ?>
    // brand-preview
    var $brand = $("#brand"),
        brandCheck = function (e) {
            var $this = $(this) || $brand;
            var $id = $this.val();
            <?php
            $prewURLs = [
                ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE,
                    SettingsPageController::L3_SLUG_PACKAGE_BRAND,
                    [
                        'view'   => 'edit',
                        'action' => 'default',
                    ]
                ),
                ControllersManager::getMenuLink(
                    ControllersManager::SETTINGS_SUBMENU_SLUG,
                    SettingsPageController::L2_SLUG_PACKAGE,
                    SettingsPageController::L3_SLUG_PACKAGE_BRAND,
                    [
                        'view'   => 'edit',
                        'action' => 'edit',
                    ]
                ),
            ];
            ?>
            var $url = <?php echo json_encode($prewURLs); ?>;
            $url[1] += "&id=" + $id;

            $("#brand-preview").attr('href', $url[ $id > 0 ? 1 : 0 ]);

            $this.find('option[value="' + $id + '"]')
                    .prop('selected', true)
                    .parent();
        };
    $brand.on('select change', brandCheck);
    <?php endif; ?>


    }(window.jQuery));
</script>
