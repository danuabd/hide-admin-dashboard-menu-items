<?php

/**
 * Debug page view for the plugin
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/partials
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Hide_Dashboard_Menu_Items_Debugger
{
    /**
     * @param array $accepted_event_types logging types to log
     */
    private $accepted_event_types;

    public function __construct()
    {
        $this->accepted_event_types = ['log', 'debug'];
    }

    private function get_environment_info()
    {
        return [
            'Plugin Version' => $this->version,
            'Environment' => [
                'WordPress Version' => get_bloginfo('version'),
                'PHP Version' => PHP_VERSION,
                'Memory Limit' => WP_MEMORY_LIMIT,
                'Active Theme' => wp_get_theme()->get('Name'),
                'Active Plugins Count' => count($this->get_plugin_option('active_plugins', [])),
            ]
        ];
    }

    public function log_event($key, $message = current_time('mysql'), $type = 'info')
    {
        if (!in_array($this->accepted_event_types, $message) || (!$message || empty($message))) {
            return;
        }

        $key = $key ?? $message;

        $debug_data = get_option($this->debug_option, []);

        $debug_data[$type][$key] = $message;
        update_option($this->debug_option, $debug_data);
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

        $scan_status = $this->get_plugin_option($this->scan_success_option, false);

        if (!$scan_status) {
            $this->log_error('Scan has not been completed. Please run the scan first.');
        }

        $db_menu_cache = get_option($this->db_menu_option, array());
        $tb_menu_cache = get_option($this->tb_menu_option, array());

        $hidden_db_menus = $this->get_plugin_option($this->hidden_db_menu_key, 'No hidden dashboard menu items configured.');
        $hidden_tb_menus = $this->get_plugin_option($this->hidden_tb_menu_key, 'No hidden admin bar menu items configured.');

        $stored_debug_data = $this->get_plugin_option($this->debug_option, []);
        $stored_info_data = $stored_debug_data['info'] ?? [];
        $stored_error_data = $stored_debug_data['error'] ?? [];

        $user = wp_get_current_user();

        $bypass_enabled = $this->get_plugin_option($this->bypass_enabled_key, false);
        $bypass_key = $this->get_plugin_option($this->bypass_param_key, false);

        if ($bypass_enabled && empty($bypass_key)) {
            $this->log_error('Bypass is enabled but no bypass key is set. Please configure the bypass key in the plugin settings.');
        }

        $curr_info_data = $this->get_environment_info();

        $curr_info_data['Database Menu Count'] = count($db_menu_cache);
        $curr_info_data['Database Menu'] = $db_menu_cache;
        $curr_info_data['Admin Bar Menu Count'] = count($tb_menu_cache);
        $curr_info_data['Admin Bar Menu'] = $tb_menu_cache;

        $curr_info_data['Current User'] = [
            'ID' => $user->ID,
            'Username' => $user->user_login,
            'Roles' => implode(', ', $user->roles),
            'Can manage_options' => current_user_can('manage_options') ? 'Yes' : 'No',
        ];

        $curr_info_data['Hidden Dashboard Menu'] = $hidden_db_menus;
        $curr_info_data['Hidden Admin Bar Menu']     = $hidden_tb_menus;
        $curr_info_data['Bypass Settings']     = [
            'Bypass Enabled' => $bypass_enabled ? 'Yes' : 'No',
            'Bypass Query Key' => $bypass_key ? 'is set' : 'is not set',
        ];

        $final_info_data = array_merge($curr_info_data, $stored_info_data);

        $debug_markup = $this->generate_debug_markup($final_info_data);
        $error_markup = empty($stored_error_data) ? '<li>No errors logged.</li>' : $this->generate_debug_markup($stored_error_data);

        require_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-debug-display.php';
    }
}
