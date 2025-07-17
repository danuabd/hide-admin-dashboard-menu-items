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
class Hide_Dashboard_Menu_Items_Options
{
    private $config;

    public function __construct(Hide_Dashboard_Menu_Items_Config $config)
    {
        $this->config = $config;
    }

    public function get($key = null, $default = null)
    {
        $options = get_option($this->config->option_name, array());

        if ($key === null) {
            return $options;
        }

        return isset($options[$key]) ? $options[$key] : $default;
    }

    public function update($key, $value)
    {
        $options = get_option($this->config->option_name, array());
        $options[$key] = $value;
        update_option($this->config->option_name, $options);
    }

    /**
     * Get the bypass active status.
     *
     * @since    1.0.0
     * @return   bool Returns true if query parameter if enabled, otherwise false.
     */
    public function is_bypass_active()
    {
        return $this->get($this->config->bypass_enabled_key, false);
    }

    public function get_dashboard_menu_cache()
    {
        return get_option($this->config->db_menu_option);
    }

    public function get_toolbar_menu_cache()
    {
        return get_option($this->config->tb_menu_option);
    }

    public function get_hidden_db_menu()
    {
        return $this->get($this->config->hidden_db_menu_key);
    }

    public function get_hidden_tb_menu()
    {
        return $this->get($this->config->hidden_tb_menu_key);
    }

    /**
     * Get the bypass query parameter if enabled.
     *
     * @since    1.0.0
     * @return   string Returns the bypass query parameter if enabled, otherwise empty string.
     */
    public function get_bypass_param()
    {
        if ($this->is_bypass_active())
            return $this->get($this->config->bypass_param_key);
        else return '';
    }

    public function update_dashboard_menu($dashboard_menu)
    {
        update_option($this->config->db_menu_option, $dashboard_menu);
    }

    public function update_toolbar_menu($toolbar_menu)
    {
        update_option($this->config->tb_menu_option, $toolbar_menu);
    }
}
