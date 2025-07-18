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
    private $config;

    private $storage_manager;

    /**
     * @param array $accepted_event_types logging types to log
     */
    private $accepted_event_types;

    public function __construct(
        Hide_Dashboard_Menu_Items_Config $config,
        Hide_Dashboard_Menu_Items_Storage_Manager $storage_manager
    ) {
        $this->config = $config;
        $this->storage_manager = $storage_manager;
        $this->accepted_event_types = ['info', 'error'];
    }

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

    private function build_debug_data()
    {

        $scan_status = $this->storage_manager->get_scan_status();

        if (!$scan_status) {
            error_log('Scan has not been completed. Please run the scan first.');
        }

        $db_menu_cache = $this->storage_manager->get_dashboard_menu_cache();
        $tb_menu_cache = $this->storage_manager->get_toolbar_menu_cache();

        $hidden_db_menus =
            $this->storage_manager->get_hidden_db_menu();
        $hidden_tb_menus =
            $this->storage_manager->get_hidden_tb_menu();

        $user = wp_get_current_user();

        $bypass_enabled = $this->storage_manager->is_bypass_active();
        $bypass_key = $this->storage_manager->get_bypass_param();

        if ($bypass_enabled && empty($bypass_key)) {
            $this->log_event('Bypass is enabled but no bypass key is set. Please configure the bypass key in the plugin settings.', 'error');
        }

        $initial_debug_data = [
            'Plugin Version' => $this->config->version,
            'Environment' => [
                'WordPress Version' => get_bloginfo('version'),
                'PHP Version' => PHP_VERSION,
                'Memory Limit' => WP_MEMORY_LIMIT,
                'Active Theme' => wp_get_theme()->get('Name'),
                'Active Plugins Count' => count(get_option('active_plugins')),
            ],
            'Current User' => [
                'ID' => $user->ID,
                'Username' => $user->user_login,
                'Roles' => implode(', ', $user->roles),
                'Can manage_options' => current_user_can('manage_options') ? 'Yes' : 'No',
            ],
            'Scan done?' => $scan_status ? 'Yes' : 'No',
            'Dashboard Menu Count' => count($db_menu_cache),
            'Dashboard Menu' => empty($db_menu_cache) ? 'No dashboard menu items were found.' : $db_menu_cache,
            'Admin Bar Menu Count' => count($tb_menu_cache),
            'Admin Bar Menu' =>  empty($tb_menu_cache) ? 'No admin bar menu items were found.' : $tb_menu_cache,
            'Hidden Dashboard Menu' => empty($hidden_db_menus) ? 'No hidden dashboard menu items configured.' : $hidden_db_menus,
            'Hidden Admin Bar Menu' => empty($hidden_tb_menus) ? 'No hidden admin bar menu items configured.' : $hidden_db_menus,
            'Bypass Settings' => [
                'Bypass Enabled' => $bypass_enabled ? 'Yes' : 'No',
                'Bypass Query Key' => $bypass_key ? 'is set' : 'is not set',
            ]
        ];

        return $initial_debug_data;
    }

    /**
     * Generate the debug array in a structured format.
     *
     * @since    1.0.0
     * @param array $array The array to generate.
     * @param int $depth The current depth of recursion.
     * @return string The generated HTML for the array.
     */
    private function generate_debug_markup($array, $depth = 0)
    {
        if (!is_array($array)) {
            return esc_html((string)$array);
        }

        $output = "<ul style='margin-left: " . (20 * $depth) . "px; list-style-type: none;'>";

        foreach ($array as $key => $value) {
            $key = esc_html((string)$key);
            if (is_array($value)) {
                $output .= "<li><strong>{$key}:</strong> " . $this->generate_debug_markup($value, $depth + 1) . "</li>";
            } else {
                $value = esc_html((string)$value);
                $output .= "<li><strong>{$key}:</strong> {$value}</li>";
            }
        }

        $output .= "</ul>";

        return $output;
    }

    /**
     * Render the debug page.
     *
     * @since    1.0.0
     */
    public function render_debug_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $stored_debug_data = $this->storage_manager->get_debug_data();

        $stored_info_data = !empty($stored_debug_data) && isset($stored_debug_data['info']) ? $stored_debug_data['info'] : [];
        $stored_error_data = !empty($stored_debug_data) && isset($stored_debug_data['error']) ? $stored_debug_data['error'] : [];

        $final_debug_info = array_merge($this->build_debug_data(), $stored_info_data);

        $debug_markup = $this->generate_debug_markup($final_debug_info);

        $error_markup = empty($stored_error_data) ? '<li>No errors logged.</li>' : $this->generate_debug_markup($stored_error_data);

        require_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-debug-display.php';
    }
}
