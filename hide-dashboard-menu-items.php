<?php

/**
 * The plugin bootstrap file
 *
 * @link              https://danukaprasad.com
 * @since             1.0.0
 * @package           Hide_Dashboard_Menu_Items
 *
 * @wordpress-plugin
 * Plugin Name:       Hide Dashboard Menu Items
 * Plugin URI:        https://danukaprasad.com/wordpress-plugins/hide-dashboard-menu-items
 * Description:       A simple & lightweight plugin that lets you hide unwanted admin menu items from the WordPress dashboard and block direct access to the associated pages — keeping your backend clean, focused, and secure.
 * Version:           1.0.0
 * Author:            ABD Prasad
 * Author URI:        https://danukaprasad.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       hide-dashboard-menu-items
 */

// If this file is called directly, abort.
if (! defined('WPINC')) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly :).';
	die;
}

/**
 * Configuration class.
 */
require_once plugin_dir_path(__FILE__) . 'includes/class-hide-dashboard-menu-items-config.php';

/**
 * Plugin name.
 */
define('HDMI_PLUGIN_NAME', 'hide-dashboard-menu-items');

/**
 * Current plugin version.
 */
define('HDMI_VERSION', '1.0.0');

/**
 * Plugin root path.
 */
define('HDMI_PLUGIN_DIR', plugin_dir_path(__FILE__));

/**
 * Plugin option prefix.
 */
define('HDMI_PREFIX', 'hdmi_by_abd');

/**
 * The code that runs during plugin activation.
 */
function activate_hide_dashboard_menu_items()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-hide-dashboard-menu-items-activator.php';
	Hide_Dashboard_Menu_Items_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_hide_dashboard_menu_items()
{
	require_once plugin_dir_path(__FILE__) . 'includes/class-hide-dashboard-menu-items-deactivator.php';
	Hide_Dashboard_Menu_Items_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_hide_dashboard_menu_items');
register_deactivation_hook(__FILE__, 'deactivate_hide_dashboard_menu_items');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-hide-dashboard-menu-items.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_hide_dashboard_menu_items()
{

	$plugin = new Hide_Dashboard_Menu_Items(Hide_Dashboard_Menu_Items_Config::settings_option());
	$plugin->run();
}
run_hide_dashboard_menu_items();
