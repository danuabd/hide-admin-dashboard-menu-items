<?php

/**
 * Fired during plugin activation.
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 * 
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/includes
 * @author     ABD Prasad <contact@danukaprasasd.com>
 */
class Hide_Dashboard_Menu_Items_Activator
{

	/**
	 * @since    1.0.0
	 */
	public static function activate()
	{
		if (version_compare(PHP_VERSION, '7.4', '<')) {
			wp_die(
				esc_html__('Hide Dashboard Menu Items requires PHP version 7.4 or higher.', 'hide-dashboard-menu-items')
			);
		}

		// Check if the current user has the capability to activate plugins
		if (! current_user_can('activate_plugins')) {
			wp_die(
				esc_html__('You do not have sufficient permissions to activate this plugin.', 'hide-dashboard-menu-items')
			);
		}

		// Check if the plugin is already activated
		if (is_plugin_active('hide-dashboard-menu-items/hide-dashboard-menu-items.php')) {
			wp_die(
				esc_html__('The Hide Dashboard Menu Items plugin is already activated.', 'hide-dashboard-menu-items')
			);
		}
	}
}
