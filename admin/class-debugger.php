<?php

/**
 * Debugger class for the plugin
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
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
     * @since   1.0.0
     * @access  private
     * @var     string   $version
     */
    private $version;

    /**
     * Storage manager class instance.
     * 
     * @since   1.0.0
     * @access  private
     * @var     Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     */
    private $storage_manager;

    /**
     * Initialize class with required instances
     * 
     * @since   1.0.0
     * @param   Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     */
    public function __construct(
        $version,
        Hide_Dashboard_Menu_Items_Storage_Manager $storage_manager
    ) {

        $this->version = $version;
        $this->storage_manager = $storage_manager;
    }

    /**
     * Add new entry to debug log.
     * 
     * @since   1.0.0
     * @param   string  $key
     * @param   string  $message
     */
    public function log_debug($key, $message)
    {
        if (!($key && $message)) {
            return;
        }

        $this->storage_manager->update_debug_log($key, $message);
    }

    /**
     * Add new entry to error log.
     * 
     * @since   1.0.0
     * @param   string  $message
     */
    public function log_error($message)
    {
        if (!$message) {
            return;
        }

        $current_time = current_time('mysql');

        $this->storage_manager->update_error_log($current_time, $message);
    }

    /**
     * Render debug page to frontend.
     *
     * @since   1.0.0
     */
    public function render_debug_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $version = $this->version;
        $current_user_id = get_current_user_id();
        $current_user = get_user_by('id', $current_user_id);
        $user_name = $current_user->display_name;
        $user_roles = implode($current_user->roles);
        $scan_status = $this->storage_manager->get_scan_status_cache();
        $dashboard_menu_cache = $this->storage_manager->get_dashboard_menu_cache();
        $admin_bar_menu_cache = $this->storage_manager->get_admin_bar_menu_cache();
        $hidden_dashboard_menu = $this->storage_manager->get_hidden_dashboard_menu();
        $hidden_admin_bar_menu = $this->storage_manager->get_hidden_admin_bar_menu();
        $bypass_enabled = $this->storage_manager->is_bypass_active();
        $bypass_key = $this->storage_manager->get_bypass_param();
        $error_log = $this->storage_manager->get_error_log_cache();
        $debug_log = $this->storage_manager->get_debug_log_cache();

        require_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-debug-display.php';
    }
}
