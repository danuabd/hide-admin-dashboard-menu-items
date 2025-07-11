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
// Check if the scan is already done
$scan_done = get_option($this->scan_success_option, false);
$hidden_menus_key = $this->hidden_menus_key;

$menu_items = get_option($this->menu_items_option, array());
$plugin_options = get_option($this->settings_option, array());
$hidden_menus = isset($plugin_options[$this->hidden_menus_key]) ? $plugin_options[$this->hidden_menus_key] : array();

if (!$scan_done && !isset($_GET['hdmi_scan_success']) || empty($menu_items)) {

    $title = $description = '';

    if (!$scan_done && !isset($_GET['hdmi_scan_success']) || !empty($menu_items)) {
        // If scan is not done, show the scan overlay
        $title = 'Welcome to Hide Admin Menu Items Plugin!';
        $description = 'Before using this plugin, you need to scan the admin menu items. Click on the button below to start the scan. This will cache the menu items and allow you to hide them later.';
    } else {
        // If scan is done but no items found, show a message
        $title = 'Admin Menu Items Scan';
        $description = 'The admin menu items have not been scanned yet. Please start the scan.';
    }

    // pass the title and description to the scan display
    extract(array(
        'title' => $title,
        'description' => $description,
    ));
    include_once __DIR__ . '/hide-dashboard-menu-items-scan-display.php';
    return;
}

// If scan is done, show success message
if (isset($_GET['hdmi_scan_success'])) {
    add_settings_error(
        'hdmi_scan_notice',
        'hdmi_scan_success',
        'Admin menu items scanned and cached successfully!',
        'success'
    );
}

settings_errors('hdmi_scan_notice');

// Only show the rest of the form if scan is done
?>
<div id="hdmi-container">
    <div class="wrap">
        <h1 id="hdmi-heading">Hide Dashboard Menu Items</h1>
        <p id="hdmi-subheading">Use the form below to hide specific dashboard menu items.</p>

        <form method="post" action="options.php" id="hdmi-settings-form">
            <?php
            // Output security fields for the registered setting "hdmi_settings"
            settings_fields($settings_option);
            // Output setting sections and their fields
            do_settings_sections($settings_page_slug);

            // Button for the re-scan request
            echo '<div id="hdmi-re-scan">';
            submit_button('Re-Scan Menu Items', 'large', 'hdmi_scan_request', false, array('value' => '1', 'id' => 'hdmi-re-scan-button'));
            echo '<span id="hdmi-re-scan-description">This will re-scan the admin menu items and update the list.</span>';
            echo '</div>';

            echo '<div class="hdmi-scanned-menu">';
            echo '<div class="hdmi-grid">';

            foreach ($menu_items as $item) {
                $slug     = esc_attr($item['slug']);
                $title    = esc_html($item['title']);
                $dashicon = esc_attr($item['dashicon']);
                $checked  = in_array($item['slug'], $hidden_menus) ? 'checked' : '';
                $status   = in_array($item['slug'], $hidden_menus) ? 'Hidden' : 'Visible';
                $name_attr = esc_attr($this->settings_option) . "[hidden_menus][]";

                echo <<<HTML
            <div class="hdmi-item">
                <div class="hdmi-icon">
                    <span class="dashicons {$dashicon}"></span>
                </div>
                <div class="hdmi-label">{$title}</div>

                <div class="hdmi-toggle">
                    <label class="hdmi-switch">
                        <input type="checkbox" name="{$name_attr}" value="{$slug}" {$checked}>
                        <span class="hdmi-slider"></span>
                    </label>
                    <small class="hdmi-toggle-label">{$status}</small>
                </div>
            </div>
            HTML;
            }

            echo '</div></div>';

            ?>

            <h2>Bypass Plugin Functionality</h2>
            <p>Use a custom query parameter to temporarily bypass hidden menu restrictions. This is useful for admins who want quick access without deactivating the plugin.</p>

            <div class="hdmi-bypass-section">
                <label class="hdmi-toggle-inline">
                    <input type="checkbox" id="hdmi-bypass-toggle" name="hdmi_bypass_enabled" value="1" />
                    <span class="hdmi-toggle-inline-slider"></span>
                    Enable bypass feature
                </label>

                <div id="hdmi-bypass-settings">
                    <label class="hdmi-bypass-input-label" for="hdmi-bypass-key"><strong>Custom Query Parameter</strong></label>
                    <input type="text" id="hdmi-bypass-key" name="hdmi_bypass_key" placeholder="e.g. bypass_access" class="regular-text" />

                    <p class="description hdmi-warning-text">
                        ⚠️ Do not use spaces, symbols like `?`, `&`, `=`, or `%`. Only letters, numbers, underscores, and hyphens are allowed.
                    </p>
                </div>
            </div>

            <?php

            // Output save settings button
            submit_button('Save changes', 'primary', 'submit', true, array('id' => 'hdmi-save-settings-button'));
            ?>
        </form>
    </div>
</div>