<?php

/**
 * Helper class for managing options of this plugin.
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/helpers
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Hide_Dashboard_Menu_Items_Storage_Manager
{

    private $config;

    public function __construct(Hide_Dashboard_Menu_Items_Config $config)
    {
        $this->config = $config;
    }

    public function get_plugin_option($key, $default = false)
    {
        $options = get_option($this->config->settings_option, array());
        return $options[$key] ?? $default;
    }

    public function get_other_option($option, $default = array())
    {
        return get_option($option, $default);
    }

    // --------------------------------------------------
    // get values using keys
    // --------------------------------------------------
    public function is_bypass_active()
    {
        return $this->get_plugin_option($this->config->bypass_enabled_key);
    }

    public function get_bypass_param()
    {
        if ($this->is_bypass_active())
            return $this->get_plugin_option($this->config->bypass_param_key);
        else return '';
    }

    public function get_hidden_db_menu()
    {
        return $this->get_plugin_option($this->config->hidden_db_menu_key, array());
    }

    public function get_hidden_tb_menu()
    {
        return $this->get_plugin_option($this->config->hidden_tb_menu_key, array());
    }

    // --------------------------------------------------
    // get data using option names
    // --------------------------------------------------
    public function get_dashboard_menu_cache()
    {
        return $this->get_other_option($this->config->db_menu_option);
    }

    public function get_toolbar_menu_cache()
    {
        return $this->get_other_option($this->config->tb_menu_option);
    }

    public function get_debug_data()
    {
        return $this->get_other_option($this->config->debug_option);
    }

    public function get_scan_status()
    {
        return $this->get_other_option($this->config->scan_success_option, false);
    }


    // --------------------------------------------------
    // update data using option name
    // --------------------------------------------------
    public function update_dashboard_menu($dashboard_menu)
    {
        if (is_array($dashboard_menu) && !empty($dashboard_menu))
            update_option($this->config->db_menu_option, $dashboard_menu);
    }

    public function update_toolbar_menu($toolbar_menu)
    {
        if (is_array($toolbar_menu) && !empty($toolbar_menu))
            update_option($this->config->tb_menu_option, $toolbar_menu);
    }

    public function update_debug_data($key, $message, $type = 'info')
    {
        if ($key && $message) {
            $debug_data = $this->get_debug_data();
            $debug_data[$type][$key] = $message;
            update_option($this->config->debug_option, $debug_data);
        }
    }
}
