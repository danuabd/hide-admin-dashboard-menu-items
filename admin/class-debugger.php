<?php

/**
 * Debugger class for the plugin
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Hide_Dashboard_Menu_Items_Debugger
{
    /**
     * Plugin version.
     *
     * @since 1.0.0
     * @access private
     * @var string
     */
    private $version;

    /**
     * Initialize class.
     *
     * @since 1.0.0
     */
    public function __construct()
    {
        $this->version = HDMI_VERSION;
    }

    /**
     * Add a new entry to the debug log.
     *
     * @since 1.0.0
     * @param string $key
     * @param string $message
     * @return void
     */
    public function log_debug($key, $message)
    {
        if (empty($key) || empty($message)) {
            return;
        }

        Hide_Dashboard_Menu_Items_Storage_Manager::update_debug_log($key, $message);
    }

    /**
     * Add a new entry to the error log.
     *
     * @since 1.0.0
     * @param string $message
     * @return void
     */
    public function log_error($message)
    {
        if (empty($message)) {
            return;
        }

        Hide_Dashboard_Menu_Items_Storage_Manager::update_error_log($message);
    }

    /**
     * Render the debug page on the admin interface.
     *
     * @since 1.0.0
     * @return void
     */
    public function render_debug_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $version = $this->version;

        $current_user = wp_get_current_user();
        $user_name = $current_user->display_name;
        $user_roles = implode(', ', $current_user->roles);

        $storage = Hide_Dashboard_Menu_Items_Storage_Manager::class;

        $scan_status      = $storage::get_scan_status_cache();
        $dashboard_cache  = $storage::get_dashboard_cache();
        $admin_bar_cache  = $storage::get_admin_bar_cache();
        $hidden_dashboard = $storage::get_hidden_dashboard();
        $hidden_admin_bar = $storage::get_hidden_admin_bar();
        $is_restrict_enabled = $storage::is_restrict_active();
        $bypass_enabled   = $storage::is_bypass_active();
        $bypass_code      = $storage::get_bypass_code();
        $error_log        = $storage::get_error_log();
        $debug_log        = $storage::get_debug_log();
        require plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-debug-display.php';
    }
}
