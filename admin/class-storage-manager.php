<?php

/**
 * Helper class for managing options of this plugin.
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
class Hide_Dashboard_Menu_Items_Storage_Manager
{

    /**
     * Instance of config class of this plugin.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Config    $config
     */
    private $config;

    /**
     * Initialize class with required instances.
     * 
     * @since   1.0.0
     * @param   Hide_Dashboard_Menu_Items_Config    $config
     */
    public function __construct(Hide_Dashboard_Menu_Items_Config $config)
    {
        $this->config = $config;
    }

    /**
     * Get plugin settings option from DB.
     * 
     * @since   1.0.0
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed   Plugin settings option.
     */
    public function get_plugin_option($key, $default = false)
    {
        $options = get_option($this->config->settings_option, array());
        return $options[$key] ?? $default;
    }

    /**
     * Get other option of the plugin from DB.
     * 
     * @since   1.0.0
     * @param   string  $key
     * @param   mixed   $default
     * @return  mixed   Other plugin options.
     */
    public function get_other_option($option, $default = array())
    {
        return get_option($option, $default);
    }

    // --------------------------------------------------
    // get values using keys
    // --------------------------------------------------

    /**
     * Check if bypass feature is active.
     * 
     * @since   1.0.0
     * @return  boolean Whether bypass is active or not.
     */
    public function is_bypass_active()
    {
        return $this->get_plugin_option($this->config->bypass_enabled_key);
    }

    /**
     * Get bypass parameter from DB.
     * 
     * @since   1.0.0
     * @return  string  Bypass query parameter.
     */
    public function get_bypass_param()
    {
        if ($this->is_bypass_active())
            return $this->get_plugin_option($this->config->bypass_param_key);
        else return '';
    }

    /**
     * Get hidden dashboard menu from DB.
     * 
     * @since   1.0.0
     * @return  array   Hidden dashboard menu.
     */
    public function get_hidden_db_menu()
    {
        return $this->get_plugin_option($this->config->hidden_db_menu_key, array());
    }

    /**
     * Get hidden admin bar menu from DB.
     * 
     * @since   1.0.0
     * @return  array   Hidden admin bar menu.
     */
    public function get_hidden_tb_menu()
    {
        return $this->get_plugin_option($this->config->hidden_tb_menu_key, array());
    }

    // --------------------------------------------------
    // get data using option names
    // --------------------------------------------------

    /**
     * Get stored dashboard menu from DB.
     * 
     * @since   1.0.0
     * @return  array   Dashboard menu cache
     */
    public function get_dashboard_menu_cache()
    {
        return $this->get_other_option($this->config->db_menu_option);
    }

    /**
     * Get stored admin bar menu from DB.
     * 
     * @since   1.0.0
     * @return  array   Admin bar menu cache
     */
    public function get_toolbar_menu_cache()
    {
        return $this->get_other_option($this->config->tb_menu_option);
    }

    /**
     * Get stored debug data from DB.
     * 
     * @since   1.0.0
     * @return  array   Debug data
     */
    public function get_debug_data()
    {
        return $this->get_other_option($this->config->debug_option);
    }

    /**
     * Get scanned status from db.
     * 
     * @since   1.0.0
     * @return  boolean     Whether a scan is done or not
     */
    public function get_scan_status()
    {
        return $this->get_other_option($this->config->scan_success_option, false);
    }


    // --------------------------------------------------
    // update data using option name
    // --------------------------------------------------

    /**
     * Update dashboard menu.
     * 
     * @since   1.0.0
     * @param   array   $dashboard_menu
     */
    public function update_dashboard_menu($dashboard_menu)
    {
        if (is_array($dashboard_menu) && !empty($dashboard_menu))
            update_option($this->config->db_menu_option, $dashboard_menu);
    }

    /**
     * Update admin bar menu.
     * 
     * @since   1.0.0
     * @param   array $toolbar_menu
     */
    public function update_toolbar_menu($toolbar_menu)
    {
        if (is_array($toolbar_menu) && !empty($toolbar_menu))
            update_option($this->config->tb_menu_option, $toolbar_menu);
    }

    /**
     * Update debug data.
     * 
     * @since   1.0.0
     * @param   array   $key
     * @param   array   $message
     * @param   array   $type
     */
    public function update_debug_data($key, $message, $type = 'info')
    {
        if ($key && $message) {
            $debug_data = $this->get_debug_data();
            $debug_data[$type][$key] = $message;
            update_option($this->config->debug_option, $debug_data);
        }
    }
}
