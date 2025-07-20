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
     * Config data of plugin.
     * 
     * @since   1.0.0
     * @access  private
     * @var     Hide_Dashboard_Menu_Items_Config    $config
     */
    private $config;

    /**
     * Storage manager of plugin.
     * 
     * @since   1.0.0
     * @access  private
     * @var     Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     */
    private $storage_manager;

    /**
     * Event types that are accepted.
     * 
     * @since   1.0.0
     * @access  private
     * @var     array   $accepted_event_types
     */
    private $accepted_event_types;

    /**
     * 
     * @since   1.0.0
     * @param   Hide_Dashboard_Menu_Items_Config            $config
     * @param   Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     */
    public function __construct(
        Hide_Dashboard_Menu_Items_Config $config,
        Hide_Dashboard_Menu_Items_Storage_Manager $storage_manager
    ) {
        $this->config = $config;
        $this->storage_manager = $storage_manager;
        $this->accepted_event_types = ['info', 'error'];
    }

    /**
     * Add event to debug data.
     * 
     * @since   1.0.0
     * @param   string  $key
     * @param   string  $message
     * @param   string  $type (info or error)
     */
    public function log_event($key = '', $message = '', $type = 'info')
    {
        if (!in_array($type, $this->accepted_event_types) || !($message || $key)) {
            return;
        }

        $current_time = current_time('mysql');

        if (!$message) {
            $message = $current_time;
        }

        if (!$key) {
            $key = $current_time;
        }

        $this->storage_manager->update_debug_data($key, $message, $type);
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

        $stored_debug_data = $this->storage_manager->get_debug_data();

        $curr_user_id = get_current_user_id();
        $user = get_user_by('id', $curr_user_id);
        $scan_status = $this->storage_manager->get_scan_status();
        $db_menu_cache = $this->storage_manager->get_dashboard_menu_cache();
        $tb_menu_cache = $this->storage_manager->get_toolbar_menu_cache();
        $hidden_db_menu = $this->storage_manager->get_hidden_db_menu();
        $hidden_tb_menu = $this->storage_manager->get_hidden_tb_menu();
        $bypass_enabled = $this->storage_manager->is_bypass_active();
        $bypass_key = $this->storage_manager->get_bypass_param();
        $stored_info_data = !empty($stored_debug_data) && isset($stored_debug_data['info']) ? $stored_debug_data['info'] : [];
        $stored_error_data = !empty($stored_debug_data) && isset($stored_debug_data['error']) ? $stored_debug_data['error'] : [];

        error_log(print_r($hidden_db_menu, true));
        error_log(print_r($hidden_tb_menu, true));

        require_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-debug-display.php';
    }
}
