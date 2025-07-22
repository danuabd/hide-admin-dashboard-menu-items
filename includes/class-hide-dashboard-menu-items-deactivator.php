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
	 * @since    1.0.1
	 */
	public static function deactivate()
	{
		require_once plugin_dir_path(__FILE__) . '../admin/class-storage-manager.php';

		Hide_Dashboard_Menu_Items_Storage_Manager::delete_plugin_data('hide-admin-dashboard-menu-items');
	}
}
