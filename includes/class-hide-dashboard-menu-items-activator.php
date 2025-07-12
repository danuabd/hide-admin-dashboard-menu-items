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
		$domain = 'hide-dashboard-menu-items';
		$hasError = false;
		$error_message = '';

		if (version_compare(PHP_VERSION, '7.4', '<')) {
			$hasError = true;
			$error_message = 'Hide Dashboard Menu Items requires PHP version 7.4 or higher.';
		}

		// Check if the current user has the capability to activate plugins
		if (! current_user_can('activate_plugins')) {
			$hasError = true;
			$error_message = 'You do not have sufficient permissions to activate this plugin.';
		}

		if ($hasError) {
			deactivate_plugins(plugin_basename(__FILE__));
			wp_die(
				esc_html__('Plugin activation failed: ', $domain) . $error_message,
				esc_html__('Plugin Activation Error', $domain),
				array('back_link' => true)
			);
		}
	}
}
