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
     * Stores cache of plugin settings.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $plugin_settings_cache   Holds plugin settings as a cache.
     */
    private $plugin_settings_cache = null;

    /**
     * Holds cache of dashboard menu.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $dashboard_menu_cache   Holds dashboard menu as a cache.
     */
    private $dashboard_menu_cache = array();

    /**
     * Holds cache of admin bar menu.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $admin_bar_menu_cache   Holds admin bar menu as a cache.
     */
    private $admin_bar_menu_cache = array();

    /**
     * Holds cache of debug log.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $debug_log_cache   Holds debug log as a cache.
     */
    private $debug_log_cache = array();

    /**
     * Holds cache of error log.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $error_log_cache   Holds error log as a cache.
     */
    private $error_log_cache = array();

    /**
     * Holds cache of scan status.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $scan_status   Holds scan status as a cache.
     */
    private $scan_status = false;

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
     * Get a saved settings from storage.
     * 
     * These are the settings can be obtained:
     * 
     * 1 - Hidden dashboard (array[]) / admin bar menu items (string[])
     * 
     * 2 - Bypass parameter (?hdmi=[parameter])
     * 
     * 3 - Bypass enabled status (true|false)
     * 
     * @since   1.0.0
     * @param   string  $setting_key
     * @param   mixed   $default_value
     * @return  mixed   Returns if the option exists in database. Otherwise the $default value.
     */
    private function get_plugin_setting($setting_key, $default_value = array())
    {
        if ($this->plugin_settings_cache === null) {

            $this->plugin_settings_cache = get_option(Hide_Dashboard_Menu_Items_Config::SETTINGS_OPTION, []);
        }

        if (empty($this->plugin_settings_cache) || !isset($this->plugin_settings_cache[$setting_key])) return $default_value;

        return $this->plugin_settings_cache;
    }

    // --------------------------------------------------
    // get values using keys
    // --------------------------------------------------

    /**
     * Check if bypass feature is set as active.
     * 
     * @since   1.0.0
     * @return  boolean     Returns true if bypass is enabled. Otherwise false.
     */
    public function is_bypass_active()
    {
        return $this->get_plugin_setting(Hide_Dashboard_Menu_Items_Config::BYPASS_STATUS_KEY, false);
    }

    /**
     * Get bypass parameter from storage.
     * 
     * @since   1.0.0
     * @return  string|null      Returns Bypass key if Bypass query parameter exists on database. Otherwise null.
     */
    public function get_bypass_param()
    {
        return $this->get_plugin_setting(Hide_Dashboard_Menu_Items_Config::BYPASS_PASSCODE_KEY, null);
    }

    /**
     * Get hidden dashboard menu from storage.
     * 
     * @since   1.0.0
     * @return  array   Returns Hidden dashboard menu if exists. Otherwise empty array.
     */
    public function get_hidden_dashboard_menu()
    {
        return $this->get_plugin_setting(Hide_Dashboard_Menu_Items_Config::HIDDEN_DASHBOARD_MENU_KEY, array());
    }

    /**
     * Get hidden admin bar menu from storage.
     * 
     * @since   1.0.0
     * @return  array   Returns Hidden admin bar menu if exists. Otherwise empty array.
     */
    public function get_hidden_admin_bar_menu()
    {
        return $this->get_plugin_setting(Hide_Dashboard_Menu_Items_Config::HIDDEN_ADMIN_BAR_MENU_KEY, array());
    }

    // --------------------------------------------------
    // get data using option names
    // --------------------------------------------------

    /**
     * Get dashboard menu.
     * 
     * @since   1.0.0
     * @return  array   Returns dashboard menu if exists in either storage or cache. Otherwise empty array.
     */
    public function get_dashboard_menu_cache()
    {
        if (empty($this->dashboard_menu_cache)) {

            $this->dashboard_menu_cache =  get_option(Hide_Dashboard_Menu_Items_Config::DASHBOARD_MENU_OPTION, array());
        }

        return $this->dashboard_menu_cache;
    }

    /**
     * Get admin bar menu.
     * 
     * @since   1.0.0
     * @return  array   Returns Admin bar menu if exists in either storage or cache. Otherwise an empty array.
     */
    public function get_admin_bar_menu_cache()
    {
        if (empty($this->admin_bar_menu_cache)) {

            $this->admin_bar_menu_cache = get_option(Hide_Dashboard_Menu_Items_Config::ADMIN_BAR_MENU_OPTION, array());
        }

        return $this->admin_bar_menu_cache;
    }

    /**
     * Get debug log.
     * 
     * @since   1.0.0
     * @return  array   Returns debug log if exists in either storage or cache. Otherwise an empty array.
     */
    public function get_debug_log_cache()
    {

        if (empty($this->debug_log_cache)) {
            $this->debug_log_cache =  get_option(Hide_Dashboard_Menu_Items_Config::DEBUG_LOG_OPTION, array());
        }

        return $this->debug_log_cache;
    }

    /**
     * Get error log.
     * 
     * @since   1.0.0
     * @return  array   Returns error log if exists in either storage or cache. Otherwise an empty array.
     */
    public function get_error_log_cache()
    {

        if (empty($this->error_log_cache)) {

            $this->error_log_cache =  get_option(Hide_Dashboard_Menu_Items_Config::ERROR_LOG_OPTION, array());
        }

        return $this->error_log_cache;
    }

    /**
     * Get menu scan status.
     * 
     * @since   1.0.0
     * @return  boolean     Returns true if exists. Otherwise false.
     */
    public function get_scan_status_cache()
    {
        if (!$this->scan_status) {

            $this->scan_status =  get_option(Hide_Dashboard_Menu_Items_Config::SCAN_SUCCESS_OPTION, false);
        }

        return $this->scan_status;
    }


    // --------------------------------------------------
    // update data using option name
    // --------------------------------------------------

    /**
     * Update dashboard menu.
     * 
     * @since   1.0.0
     * @param   array   $dashboard_menu     Dashboard menu to update.
     * @return boolean                      Returns true if updated. Otherwise false.
     */
    public function update_dashboard_menu($dashboard_menu)
    {
        $updated = false;

        if (is_array($dashboard_menu) && !empty($dashboard_menu))
            $updated =  update_option(Hide_Dashboard_Menu_Items_Config::DASHBOARD_MENU_OPTION, $dashboard_menu);

        return $updated;
    }

    /**
     * Update admin bar menu.
     * 
     * @since   1.0.0
     * @param   array   $admin_bar_menu     Admin bar menu to update.
     * @return  boolean                     Returns true if updated. Otherwise false.
     */
    public function update_admin_bar_menu($admin_bar_menu)
    {
        $updated = false;

        if (is_array($admin_bar_menu) && !empty($admin_bar_menu))
            $updated =  update_option(Hide_Dashboard_Menu_Items_Config::ADMIN_BAR_MENU_OPTION, $admin_bar_menu);

        return $updated;
    }

    /**
     * Update debug log entry.
     * 
     * @since   1.0.0
     * @param   array   $key        Key to find the correct entry to update.
     * @param   array   $message    New value.
     * @return  boolean             Returns true if updated. Otherwise false.
     */
    public function update_debug_log($key, $message)
    {
        $updated = false;

        if (!($key && $message)) return $updated;

        $debug_data = $this->get_debug_log_cache();
        $debug_data[$key] = $message;
        $updated =  update_option(Hide_Dashboard_Menu_Items_Config::DEBUG_LOG_OPTION, $debug_data);

        return $updated;
    }

    /**
     * Update error log entry.
     * 
     * @since   1.0.0
     * @param   array   $key        Key to find the correct entry to update.
     * @param   array   $message    New value.
     * @return  boolean             Returns true if updated. Otherwise false.
     */
    public function update_error_log($message)
    {
        $updated = false;

        if (!$message) return $updated;

        // current data
        $key = current_time('mysql');

        $error_log = $this->get_error_log_cache();
        $error_log[$key] = $message;
        $updated =  update_option(Hide_Dashboard_Menu_Items_Config::ERROR_LOG_OPTION, $error_log);

        return $updated;
    }
}
