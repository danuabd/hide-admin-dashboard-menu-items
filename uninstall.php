<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://danukaprasad.com
 * @since      1.0.1
 *
 * @package    Hide_Dashboard_Menu_Items
 */

// If uninstall not called from WordPress, then exit.
if (! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}


delete_option(Hide_Dashboard_Menu_Items_Config::settings_option());
delete_option(Hide_Dashboard_Menu_Items_Config::dashboard_option());
delete_option(Hide_Dashboard_Menu_Items_Config::admin_bar_option());
delete_option(Hide_Dashboard_Menu_Items_Config::debug_option());
delete_option(Hide_Dashboard_Menu_Items_Config::error_option());
delete_option(Hide_Dashboard_Menu_Items_Config::scan_success_option());
