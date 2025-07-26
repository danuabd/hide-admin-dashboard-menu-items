<?php

/**
 * Admin area view for the plugin
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.1
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/partials
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$settings_option = Hide_Dashboard_Menu_Items_Config::settings_option();
$bypass_code_key = Hide_Dashboard_Menu_Items_Config::bypass_passcode_key();
$restrict_status_key = Hide_Dashboard_Menu_Items_Config::restrict_status_key();
$bypass_status_key = Hide_Dashboard_Menu_Items_Config::bypass_status_key();
$hidden_dashboard_key = Hide_Dashboard_Menu_Items_Config::hidden_dashboard_key();
$hidden_admin_bar_key = Hide_Dashboard_Menu_Items_Config::hidden_admin_bar_key();

if (!$scan_completed || (!$dashboard && !$admin_bar)) {

    $title = $description = '';

    if ($scan_completed && (!$dashboard && !$admin_bar)) {
        // If initial scan is not completed
        $title = 'Welcome to Hide Admin Menu Items Plugin!';
        $description = 'Before using this plugin, you need to scan the admin menu items. Click on the button below to start the scan. This will cache the dashboard menu and toolbar menu items and allow you to hide them later.';
    } else {
        // If scan is completed but no items found
        $title = 'Admin Menu Items Scan';
        $description = 'The admin menu items have not been scanned yet or corrupted. Please start the scan.';
    }

    require_once __DIR__ . '/hide-dashboard-menu-items-scan-display.php';
    return;
}
?>
<div id="hdmi">
    <div class="wrap">
        <h1 id="hdmi__title">Hide Dashboard Menu Items</h1>
        <p id="hdmi__subtitle">Use the form below to hide specific dashboard menu items.</p>

        <div id="hdmi__scan-wrapper">
            <form method="post" id="hdmi__scan-form">
                <div id="hdmi__scan-inner">
                    <?php
                    wp_nonce_field('_hdmi_re_scan_nonce_action', '_hdmi_re_scan_nonce_field');
                    submit_button('Re-Scan Menu Items', 'large', 'submit', false, array('id' => 'hdmi__rescan-button'));
                    ?>
                    <span id="hdmi__scan-inner-description">This will re-scan the admin menu items and update the list.</span>
                </div>

            </form>
        </div>

        <div id="hdmi__setting-wrapper">
            <form method="post" action="options.php" id="hdmi__setting-form">
                <?php
                settings_fields(Hide_Dashboard_Menu_Items_Config::option_group());
                do_settings_sections($settings_page_slug);
                settings_errors();

                /* ---------------------------------------------------
                    ADMIN Dashboard ITEMS
                --------------------------------------------------- */
                if (!empty($dashboard)) {
                    echo '<h2 class="hdmi__subheading">Dashboard Menu Items</h2>';
                    echo '<div class="hdmi__menu" id="hdmi__menu--db">';
                    echo '<div class="hdmi__grid">';

                    foreach ($dashboard as $item) {
                        $slug     = $item['slug'];
                        $title    = $item['title'];
                        $dashicon = $item['dashicon'];
                        $checked  = in_array($item['slug'], $hidden_dashboard) ? 'checked' : '';
                        $status   = in_array($item['slug'], $hidden_dashboard) ? 'Hidden' : 'Visible';
                        $name_attr = "{$settings_option}[{$hidden_dashboard_key}][]";

                ?>
                        <div class="hdmi__grid-item">
                            <div class="hdmi__grid-item-icon">
                                <span class="dashicons <?php echo esc_attr($dashicon) ?>"></span>
                            </div>
                            <div class="hdmi__item-label"><?php echo esc_html($title) ?></div>

                            <div class="hdmi__item-toggle">
                                <label class="hdmi-toggle-wrapper">
                                    <input type="checkbox" name="<?php echo esc_attr($name_attr) ?>" value="<?php echo esc_attr($slug) ?>" class="hdmi-toggle-input" <?php echo esc_attr($checked) ?>>
                                    <span class="hdmi-toggle-slider"></span>
                                </label>
                                <small class="hdmi__item-status"><?php echo esc_html($status) ?></small>
                            </div>
                        </div>
                    <?php
                    }

                    echo '</div></div>';
                }

                /* ---------------------------------------------------
                    ADMIN TOOLBAR ITEMS
                --------------------------------------------------- */
                if (isset($admin_bar) && !empty($admin_bar)) {
                    echo '<h2 class="hdmi__subheading">Toolbar Menu Items</h2>';
                    echo '<div id="hdmi__menu--tb">';
                    echo '<div class="hdmi__list">';

                    foreach ($admin_bar as $item) {
                        $id     = $item['id'];
                        $title    = $item['title'];
                        $checked  = in_array($id, $hidden_admin_bar) ? 'checked' : '';
                        $status   = in_array($id, $hidden_admin_bar) ? 'Hidden' : 'Visible';
                        $name_attr = "{$settings_option}[{$hidden_admin_bar_key}][]";

                    ?>

                        <div class="hdmi__list-item">
                            <span class="hdmi__list-title"><?php echo esc_html($title) ?></span>
                            <div class="hdmi__list-controls">
                                <label class="hdmi-toggle-wrapper">
                                    <input type="checkbox" name="<?php echo esc_attr($name_attr) ?>" value="<?php echo esc_attr($id) ?>" class="hdmi-toggle-input" <?php echo esc_attr($checked) ?>>
                                    <span class="hdmi-toggle-slider"></span>
                                </label>
                                <small class="hdmi__list-status"><?php echo esc_html($status) ?></small>
                            </div>
                        </div>
                <?php
                    }

                    echo '</div></div>';
                }

                ?>

                <!-------------------------------------------------------
                    RESTRICT
                <-------------------------------------------------------->
                <div id="hdmi__restrict-settings">
                    <h2 id="hdmi__restrict-heading" class="hdmi__subheading">Restrict Menu Access</h2>
                    <p id="hdmi__restrict-description">Enable this option if you want restrict access to users who try to access hidden menu, and its submenu items.</p>

                    <div id="hdmi__restrict-controls">
                        <label id="hdmi__restrict-toggle-wrapper" class="hdmi-toggle-wrapper">
                            <input type="checkbox" id="hdmi__restrict-toggle" class="hdmi-toggle-input"
                                name="<?php echo esc_attr("{$settings_option}[{$restrict_status_key}]") ?>" value="1"
                                <?php echo esc_attr($is_restrict_enabled)  ? 'checked' : '' ?> />
                            <span id="hdmi__restrict-slider" class="hdmi-toggle-slider"></span>
                            Enable restriction.
                        </label>
                    </div>
                </div>
                <?php

                ?>


                <!-------------------------------------------------------
                    BYPASS
                <------------------------------------------------------->
                <div id="hdmi__bypass">
                    <h2 id="hdmi__bypass-heading" class="hdmi__subheading">Bypass Plugin Functionality</h2>
                    <p id="hdmi__bypass-description">Set a passcode to bypass hidden menu restrictions. This allows administrators to access hidden menu pages using direct link.</p>

                    <div id="hdmi__bypass-controls">
                        <label id="hdmi__bypass-toggle-wrapper" class="hdmi-toggle-wrapper">
                            <input type="checkbox" id="hdmi__bypass-toggle" class="hdmi-toggle-input"
                                name="<?php echo esc_attr("{$settings_option}[{$bypass_status_key}]") ?>" value="1"
                                <?php echo esc_attr($is_bypass_enabled)  ? 'checked' : '' ?> />
                            <span id="hdmi__bypass-slider" class="hdmi-toggle-slider"></span>
                            Enable bypass feature
                        </label>

                        <div id="hdmi__bypass-settings">
                            <label id="hdmi__bypass-label" for="hdmi__bypass-key"><strong>Set Bypass Passcode:</strong></label>
                            <input type="text" id="hdmi__bypass-key" name="<?php echo esc_attr("{$settings_option}[{$bypass_code_key}]") ?>" value="<?php echo esc_attr($bypass_code) ?>" placeholder="e.g. bypass_access" class="regular-text" minlength="4" maxlength="12" autocomplete="off" disabled />

                            <p id="description hdmi__bypass-warning">
                                ⚠️ Do not use spaces, symbols like `?`, `&`, `=`, or `%`. Only letters, numbers, underscores, and hyphens are allowed.
                            </p>
                        </div>
                    </div>
                </div>

                <?php

                // save settings
                submit_button('Save changes', 'primary', 'submit', true, array('id' => 'hdmi__save-button', 'class' => 'hdmi__button'));
                ?>
            </form>
        </div>
    </div>
</div>