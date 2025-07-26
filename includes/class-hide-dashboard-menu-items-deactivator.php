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
	 * Deactivate plugin
	 * 
	 * @since    1.0.1
	 */
	public static function deactivate()
	{

		delete_option(Hide_Dashboard_Menu_Items_Config::dashboard_option());
		delete_option(Hide_Dashboard_Menu_Items_Config::admin_bar_option());
		delete_option(Hide_Dashboard_Menu_Items_Config::debug_option());
		delete_option(Hide_Dashboard_Menu_Items_Config::scan_success_option());
	}
}
