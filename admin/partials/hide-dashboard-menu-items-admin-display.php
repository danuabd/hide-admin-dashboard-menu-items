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
$scan_done = get_option('hdmi_scan_completed');

$menu_items = get_option($this->menu_items_option_name, array());
$plugin_options = get_option($this->settings_option_name, array());
$hidden_menus = isset($plugin_options['hidden_menus']) ? $plugin_options['hidden_menus'] : array();

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
    require_once __DIR__ . '/hide-dashboard-menu-items-scan-display.php';
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
if (get_option('hdmi_scan_completed')):
?>
    <div id="hdmi-container">
        <div class="wrap">
            <h1>Hide Dashboard Menu Items</h1>
            <p>Use the form below to hide specific dashboard menu items.</p>

            <form method="post" action="options.php" id="hdmi-settings-form">
                <?php
                // Output security fields for the registered setting "hdmi_settings"
                settings_fields($settings_option_name);
                // Output setting sections and their fields
                do_settings_sections($settings_page_slug);

                // Button for the re-scan request
                echo '<div class="hdmi-re-scan">';
                submit_button('Re-Scan Menu Items', 'primary', 'hdmi_scan_request', false, array('value' => '1'));
                echo '<span class="hdmi-re-scan-description">This will re-scan the admin menu items and update the list.</span>';
                echo '</div>';

                echo '<div class="hdmi-scanned-menu">';
                echo '<div class="hdmi-grid">';

                foreach ($menu_items as $item) {
                    $slug     = esc_attr($item['slug']);
                    $title    = esc_html($item['title']);
                    $dashicon = esc_attr($item['dashicon']);
                    $checked  = in_array($item['slug'], $hidden_menus) ? 'checked' : '';
                    $status   = in_array($item['slug'], $hidden_menus) ? 'Hidden' : 'Visible';
                    $name_attr = esc_attr($this->settings_option_name) . "[hidden_menus][]";

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

                // Output save settings button
                submit_button();
                ?>
            </form>
        </div>
    </div>
<?php endif; ?>