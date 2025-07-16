<?php


/**
 * Fired during plugin deactivation.
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 * 
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/includes
 * @author     ABD Prasad <contact@danukaprasasd.com>
 */
class Hide_Dashboard_Menu_Items_Deactivator
{

	/**
	 * @since    1.0.0
	 */
	public static function deactivate()
	{
		$plugin_option_name = 'hdmi';

		// Delete the options set by the plugin
		delete_option($plugin_option_name . '_tb_cached');
		delete_option($plugin_option_name . '_db_cached');
		delete_option($plugin_option_name . '_scan_completed');
		delete_option($plugin_option_name . '_debug');
	}
}
