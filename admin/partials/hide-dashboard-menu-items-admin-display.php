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

// Get the cached menu items and plugin options
$plugin_options = get_option($this->settings_option, array());
$cached_menu_items = get_option($this->menu_items_option, array());
$hidden_menu_items = isset($plugin_options[$this->hidden_menus_key]) ? $plugin_options[$this->hidden_menus_key] : array();

$bypass_enabled = !empty($plugin_options[$this->bypass_enabled_key]) ? 'checked' : '';
$bypass_value = isset($plugin_options[$this->bypass_query_key]) ? esc_attr($plugin_options[$this->bypass_query_key]) : '';


if (!$scan_done && !isset($_GET['hdmi_scan_success']) || !$cached_menu_items) {

    $title = $description = '';

    if (!$scan_done && !isset($_GET['hdmi_scan_success']) || !$cached_menu_items) {
        // If scan is not done, show the scan overlay
        $title = 'Welcome to Hide Admin Menu Items Plugin!';
        $description = 'Before using this plugin, you need to scan the admin menu items. Click on the button below to start the scan. This will cache the menu items and allow you to hide them later.';
    } else {
        // If scan is done but no items found, show a message
        $title = 'Admin Menu Items Scan';
        $description = 'The admin menu items have not been scanned yet. Please start the scan.';
    }

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
<div id="hdmi">
    <div class="wrap">
        <h1 id="hdmi__heading">Hide Dashboard Menu Items</h1>
        <p id="hdmi__subheading">Use the form below to hide specific dashboard menu items.</p>

        <form method="post" action="options.php" id="hdmi__form">
            <?php
            // Output security fields for the registered setting "hdmi_settings"
            settings_fields($this->plugin_option_group);
            // Output setting sections and their fields
            do_settings_sections($this->settings_page_slug);

            // Button for the re-scan request
            echo '<div id="hdmi__rescan">';
            submit_button('Re-Scan Menu Items', 'large', 'hdmi_scan_request', false, array('value' => '1', 'id' => 'hdmi__rescan-button'));
            echo '<span id="hdmi__rescan-description">This will re-scan the admin menu items and update the list.</span>';
            echo '</div>';

            echo '<div class="hdmi__scanned-menu">';
            echo '<div class="hdmi__grid">';

            foreach ($cached_menu_items as $item) {
                $slug     = esc_attr($item['slug']);
                $title    = esc_html($item['title']);
                $dashicon = esc_attr($item['dashicon']);
                $checked  = in_array($item['slug'], $hidden_menu_items) ? 'checked' : '';
                $status   = in_array($item['slug'], $hidden_menu_items) ? 'Hidden' : 'Visible';
                $name_attr = esc_attr($this->settings_option . "[$this->hidden_menus_key][]");

                echo <<<HTML
            <div class="hdmi__item">
                <div class="hdmi__item-icon">
                    <span class="dashicons {$dashicon}"></span>
                </div>
                <div class="hdmi__item-label">{$title}</div>

                <div class="hdmi__item-toggle">
                    <label class="hdmi__item-switch">
                        <input type="checkbox" name="{$name_attr}" value="{$slug}" {$checked}>
                        <span class="hdmi__item-slider"></span>
                    </label>
                    <small class="hdmi__item-status">{$status}</small>
                </div>
            </div>
            HTML;
            }

            echo '</div></div>';

            ?>

            <div id="hdmi__bypass">
                <h2 id="hdmi__bypass-heading">Bypass Plugin Functionality</h2>
                <p id="hdmi__bypass-description">Use a custom query parameter to temporarily bypass hidden menu restrictions. This is useful for admins who want quick access without deactivating the plugin.</p>

                <div class="hdmi__bypass-controls">
                    <label class="hdmi__bypass-toggle-wrapper">
                        <input type="checkbox" id="hdmi__bypass-toggle"
                            name="<?php echo esc_attr($this->settings_option . "[{$this->bypass_enabled_key}]") ?>" value="1" <?php echo $bypass_enabled ?> />
                        <span class="hdmi__bypass-slider"></span>
                        Enable bypass feature
                    </label>

                    <div id="hdmi__bypass-settings">
                        <label class="hdmi__bypass-label" for="hdmi__bypass-key"><strong>Custom Query Parameter</strong></label>
                        <input type="text" id="hdmi__bypass-key" name="<?php echo esc_attr($this->settings_option . "[{$this->bypass_query_key}]") ?>" value="<?php echo $bypass_value ?>" placeholder="e.g. bypass_access" class="regular-text" />

                        <p class="description hdmi__bypass-warning">
                            ⚠️ Do not use spaces, symbols like `?`, `&`, `=`, or `%`. Only letters, numbers, underscores, and hyphens are allowed.
                        </p>
                    </div>
                </div>
            </div>

            <?php

            // Output save settings button
            submit_button('Save changes', 'primary', 'submit', true, array('id' => 'hdmi__save-button'));
            ?>
        </form>
    </div>
</div>