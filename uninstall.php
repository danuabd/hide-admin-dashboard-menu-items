<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 */

// If uninstall not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

require_once plugin_dir_path(__FILE__) . './admin/class-storage-manager.php';

Hide_Dashboard_Menu_Items_Storage_Manager::delete_plugin_data('hide-admin-dashboard-menu-items');
