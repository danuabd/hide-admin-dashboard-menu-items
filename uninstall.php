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
$plugin_name = 'hide_dashboard_menu_items';

// Delete the options set by the plugin
delete_option($plugin_name . '_settings');
delete_option($plugin_name . '_menu_items');
delete_option($plugin_name . '_scan_success');
