<?php

/**
 * Admin area view for the plugin
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/partials
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$settings_option = esc_attr(Hide_Dashboard_Menu_Items_Config::SETTINGS_OPTION);
$bypass_passcode_key = esc_attr(Hide_Dashboard_Menu_Items_Config::BYPASS_PASSCODE_KEY);
$bypass_status_key = esc_attr(Hide_Dashboard_Menu_Items_Config::BYPASS_STATUS_KEY);
$admin_bar_menu_key = esc_attr(Hide_Dashboard_Menu_Items_Config::HIDDEN_ADMIN_BAR_MENU_KEY);
$dashboard_menu_key = esc_attr(Hide_Dashboard_Menu_Items_Config::HIDDEN_DASHBOARD_MENU_KEY);

if (!$scan_completed && !isset($_GET['hdmi_scan_success']) || (!$dashboard_menu && !$admin_bar_menu)) {

    $title = $description = '';

    if (!$scan_completed && !isset($_GET['hdmi_scan_success']) || !$dashboard_menu && !$admin_bar_menu) {
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

// Only show the rest of the form if a scan is completed and menu items present
?>
<div id="hdmi">
    <div class="wrap">
        <h1 id="hdmi__title">Hide Dashboard Menu Items</h1>
        <p id="hdmi__subtitle">Use the form below to hide specific dashboard menu items.</p>

        <form method="post" action="options.php" id="hdmi__form">
            <?php
            settings_fields(Hide_Dashboard_Menu_Items_Config::OPTION_GROUP);
            do_settings_sections($settings_page_slug);
            wp_nonce_field('hdmi_scan_nonce_action', 'hdmi_scan_nonce_field');

            echo '<div id="hdmi__rescan">';
            submit_button('Re-Scan Menu Items', 'large', 'hdmi_scan_request', false, array('value' => '1', 'id' => 'hdmi__rescan-button', 'class' => 'hdmi__button'));
            echo '<span id="hdmi__rescan-description">This will re-scan the admin menu items and update the list.</span>';
            echo '</div>';

            /* ---------------------------------------------------
                ADMIN Dashboard ITEMS
            --------------------------------------------------- */
            if (!empty($dashboard_menu)) {
                echo '<h2 class="hdmi__subheading">Dashboard Menu Items</h2>';
                echo '<div class="hdmi__menu" id="hdmi__menu--db">';
                echo '<div class="hdmi__grid">';

                foreach ($dashboard_menu as $item) {
                    $slug     = $item['slug'];
                    $title    = $item['title'];
                    $dashicon = $item['dashicon'];
                    $checked  = in_array($item['slug'], $hidden_dashboard_menu) ? 'checked' : '';
                    $status   = in_array($item['slug'], $hidden_dashboard_menu) ? 'Hidden' : 'Visible';
                    $name_attr = "{$settings_option}[{$dashboard_menu_key}][]";

            ?>
                    <div class="hdmi__grid-item">
                        <div class="hdmi__grid-item-icon">
                            <span class="dashicons <?php echo esc_attr($dashicon) ?>"></span>
                        </div>
                        <div class="hdmi__item-label"><?php echo esc_html($title) ?></div>

                        <div class="hdmi__item-toggle">
                            <label class="hdmi-toggle-wrapper">
                                <input type="checkbox" name="<?php echo esc_attr($name_attr) ?>" value="<?php echo esc_attr($slug) ?>>" class="hdmi-toggle-input" <?php echo esc_attr($checked) ?>>
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
            if (isset($admin_bar_menu) && !empty($admin_bar_menu)) {
                echo '<h2 class="hdmi__subheading">Toolbar Menu Items</h2>';
                echo '<div id="hdmi__menu--tb">';
                echo '<div class="hdmi__list">';

                foreach ($admin_bar_menu as $item) {
                    $id     = $item['id'];
                    $title    = $item['title'];
                    $checked  = in_array($id, $hidden_admin_bar_menu) ? 'checked' : '';
                    $status   = in_array($id, $hidden_admin_bar_menu) ? 'Hidden' : 'Visible';
                    $name_attr = "{$settings_option}[{$admin_bar_menu_key}][]";

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

            <!------------------------------------------------------->
            <!-- BYPASS -->
            <!------------------------------------------------------->
            <div id="hdmi__bypass">
                <h2 id="hdmi__bypass-heading" class="hdmi__subheading">Bypass Plugin Functionality</h2>
                <p id="hdmi__bypass-description">Use a custom query parameter to temporarily bypass hidden menu restrictions. This allows administrators to access hidden menu pages directly by visiting the menu link with the query automatically appended—no need to disable the plugin.</p>

                <div id="hdmi__bypass-controls">
                    <label id="hdmi__bypass-toggle-wrapper" class="hdmi-toggle-wrapper">
                        <input type="checkbox" id="hdmi__bypass-toggle" class="hdmi-toggle-input"
                            name="<?php echo esc_attr("{$settings_option}[{$bypass_status_key}]") ?>" value="1"
                            <?php echo esc_attr($is_bypass_enabled)  ? 'checked' : '' ?> />
                        <span id="hdmi__bypass-slider" class="hdmi-toggle-slider"></span>
                        Enable bypass feature
                    </label>

                    <div id="hdmi__bypass-settings">
                        <label id="hdmi__bypass-label" for="hdmi__bypass-key"><strong>Custom Query Parameter</strong></label>
                        <input type="text" id="hdmi__bypass-key" name="<?php echo esc_attr("{$settings_option}[{$bypass_passcode_key}]") ?>" value="<?php echo esc_attr($bypass_parameter) ?>" placeholder="e.g. bypass_access" class="regular-text" minlength="4" maxlength="12" disabled />

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