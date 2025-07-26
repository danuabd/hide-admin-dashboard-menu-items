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
     * Stores cache of plugin settings.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $settings_cache   Holds plugin settings as a cache.
     */
    private static $settings_cache = null;

    /**
     * Holds cache of dashboard menu.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $dashboard_cache   Holds dashboard menu as a cache.
     */
    private static $dashboard_cache = array();

    /**
     * Holds cache of dashboard menu children.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $dashboard_children_cache   Holds dashboard menu children as a cache.
     */
    private static $dashboard_children_cache = array();

    /**
     * Holds cache of admin bar menu.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $admin_bar_cache   Holds admin bar menu as a cache.
     */
    private static $admin_bar_cache = array();

    /**
     * Holds cache of admin bar menu children.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $admin_bar_children_cache   Holds admin bar menu children as a cache.
     */
    private static $admin_bar_children_cache = array();

    /**
     * Holds cache of debug log.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $debug_log   Holds debug log as a cache.
     */
    private static $debug_log = array();

    /**
     * Holds cache of error log.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $error_log   Holds error log as a cache.
     */
    private static $error_log = array();

    /**
     * Holds cache of scan status.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     array[]    $scan_status   Holds scan status as a cache.
     */
    private static $scan_status = false;

    /**
     * Retrieve an option.
     *
     * @since 1.0.1
     * @param string    $option_name    The option to retrieve.
     * @param mixed     $default        Default value.
     * @return mixed
     */
    private static function get_array_option($option_name, $default = [])
    {
        return get_option($option_name, $default);
    }

    /**
     * Update an entry in option.
     *
     * @since 1.0.1
     * @param string    $option_name    The key to retrieve from plugin settings.
     * @param string    $key            Key to find the entry.
     * @param mixed     $value          Value to update.
     * @return mixed
     */
    private static function update_array_option($option_name, $key, $value)
    {
        $data = self::get_array_option($option_name, []);
        $data[$key] = $value;
        return update_option($option_name, $data);
    }

    /**
     * Retrieve a specific key from a multidimensional option array (with lazy cache).
     *
     * @since 1.0.1
     * @param string $key           The key to retrieve from plugin settings.
     * @param mixed  $default       Default value if not found.
     * @return mixed
     */
    private static function get_plugin_setting($key, $default = null)
    {
        if (self::$settings_cache === null) {
            self::$settings_cache = self::get_array_option(Hide_Dashboard_Menu_Items_Config::settings_option());
        }

        return isset(self::$settings_cache[$key]) ? self::$settings_cache[$key] : $default;
    }

    /**
     * Update a specific key inside plugin settings array.
     *
     * @since 1.0.1
     * @param string $key
     * @param mixed  $value
     * @return bool
     */
    private static function update_plugin_setting($key, $value)
    {
        if ($value === null) return false;

        self::update_array_option(Hide_Dashboard_Menu_Items_Config::settings_option(), $key, $value);
    }



    /* --------------------------------------------------
        get values using keys
    ----------------------------------------------------- */

    /**
     * Check if submenu restriction is enabled.
     * 
     * @since   1.0.1
     * @return  boolean     Returns true if submenu restrict is enabled. Otherwise false.
     */
    public static function is_restrict_active()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::restrict_status_key(), false);
    }

    /**
     * Check if bypass feature is set as active.
     * 
     * @since   1.0.1
     * @return  boolean     Returns true if bypass is enabled. Otherwise false.
     */
    public static function is_bypass_active()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::bypass_status_key(), false);
    }

    /**
     * Get bypass parameter from storage.
     * 
     * @since   1.0.1
     * @return  string|null      Returns Bypass key if Bypass parameter exists on database. Otherwise null.
     */
    public static function get_bypass_code()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::bypass_passcode_key(), null);
    }

    /**
     * Get hidden dashboard menu from storage.
     * 
     * @since   1.0.1
     * @return  array   Returns Hidden dashboard menu if exists. Otherwise empty array.
     */
    public static function get_hidden_dashboard()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::hidden_dashboard_key(), array());
    }

    /**
     * Get restricted dashboard menu from storage.
     * 
     * @since   1.0.1
     * @return  array   Returns Restricted dashboard menu if exists. Otherwise empty array.
     */
    public static function get_restricted_dashboard()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::restricted_dashboard_key(), array());
    }

    /**
     * Get hidden admin bar menu from storage.
     * 
     * @since   1.0.1
     * @return  array   Returns Hidden admin bar menu if exists. Otherwise empty array.
     */
    public static function get_hidden_admin_bar()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::hidden_admin_bar_key(), array());
    }

    /**
     * Get restricted admin bar menu from storage.
     * 
     * @since   1.0.1
     * @return  array   Returns Restricted admin bar menu if exists. Otherwise empty array.
     */
    public static function get_restricted_admin_bar()
    {
        return self::get_plugin_setting(Hide_Dashboard_Menu_Items_Config::restricted_admin_bar_key(), array());
    }

    /**
     * Get scan status.
     * 
     * @since   1.0.1
     * @return  boolean   Returns true if scan has started. Otherwise false.
     */
    public static function has_scan_started()
    {
        return get_transient('hdmi_scan_has_started');
    }

    /* --------------------------------------------------
        get data using option names
    ----------------------------------------------------- */

    /**
     * Get dashboard menu.
     * 
     * @since   1.0.1
     * @return  array   Returns dashboard menu if exists in either storage or cache. Otherwise empty array.
     */
    public static function get_dashboard_cache()
    {
        if (empty(self::$dashboard_cache)) {

            self::$dashboard_cache =  self::get_array_option(Hide_Dashboard_Menu_Items_Config::dashboard_option());
        }

        return self::$dashboard_cache;
    }

    /**
     * Get dashboard menu children cache.
     * 
     * @since   1.0.1
     * @return  array   Returns dashboard menu children if exists in either storage or cache. Otherwise empty array.
     */
    public static function get_dashboard_children_cache()
    {
        if (empty(self::$dashboard_children_cache)) {

            self::$dashboard_children_cache =  self::get_array_option(Hide_Dashboard_Menu_Items_Config::dashboard_children_option());
        }

        return self::$dashboard_children_cache;
    }

    /**
     * Get admin bar menu.
     * 
     * @since   1.0.1
     * @return  array   Returns Admin bar menu if exists in either storage or cache. Otherwise an empty array.
     */
    public static function get_admin_bar_cache()
    {
        if (empty(self::$admin_bar_cache)) {

            self::$admin_bar_cache = self::get_array_option(Hide_Dashboard_Menu_Items_Config::admin_bar_option());
        }

        return self::$admin_bar_cache;
    }

    /**
     * Get admin bar menu children.
     * 
     * @since   1.0.1
     * @return  array   Returns Admin bar menu children if exists in either storage or cache. Otherwise an empty array.
     */
    public static function get_admin_bar_children_cache()
    {
        if (empty(self::$admin_bar_children_cache)) {

            self::$admin_bar_children_cache = self::get_array_option(Hide_Dashboard_Menu_Items_Config::admin_bar_children_option());
        }

        return self::$admin_bar_children_cache;
    }

    /**
     * Get debug log.
     * 
     * @since   1.0.1
     * @return  array   Returns debug log if exists in either storage or cache. Otherwise an empty array.
     */
    public static function get_debug_log()
    {
        self::$debug_log =  self::get_array_option(Hide_Dashboard_Menu_Items_Config::debug_option());

        return self::$debug_log;
    }

    /**
     * Get error log.
     * 
     * @since   1.0.1
     * @return  array   Returns error log if exists in either storage or cache. Otherwise an empty array.
     */
    public static function get_error_log()
    {
        self::$error_log =  self::get_array_option(Hide_Dashboard_Menu_Items_Config::error_option());

        return self::$error_log;
    }

    /**
     * Get menu scan status.
     * 
     * @since   1.0.1
     * @return  boolean     Returns true if exists. Otherwise false.
     */
    public static function get_scan_status_cache()
    {
        if (!self::$scan_status) {

            self::$scan_status =  self::get_array_option(Hide_Dashboard_Menu_Items_Config::scan_success_option(), false);
        }

        return self::$scan_status;
    }

    /* --------------------------------------------------
        update data using options
    ----------------------------------------------------- */

    /**
     * Update dashboard menu.
     * 
     * @since   1.0.1
     * @param   array   $dashboard     Dashboard menu to update.
     * @return boolean                      Returns true if updated. Otherwise false.
     */
    public static function update_dashboard($dashboard)
    {
        $updated = false;

        if (is_array($dashboard) && !empty($dashboard))
            $updated =  update_option(Hide_Dashboard_Menu_Items_Config::dashboard_option(), $dashboard);

        return $updated;
    }

    /**
     * Update dashboard menu children.
     * 
     * @since   1.0.1
     * @param   array   $dashboard_children           Children items to update.
     * @return  boolean                     Returns true if updated. Otherwise false.
     */
    public static function update_dashboard_children($dashboard_children)
    {
        $updated = false;

        if (is_array($dashboard_children) && !empty($dashboard_children))
            $updated =  update_option(Hide_Dashboard_Menu_Items_Config::dashboard_children_option(), $dashboard_children);

        return $updated;
    }

    /**
     * Update restricted dashboard menu.
     * 
     * @since   1.0.1
     * @param   array   $restricted_dashboard       updated menu.
     * @return  boolean                             Returns true if updated. Otherwise false.
     */
    public static function update_restricted_dashboard($restricted_dashboard)
    {
        return self::update_plugin_setting(Hide_Dashboard_Menu_Items_Config::restricted_dashboard_key(), $restricted_dashboard);
    }

    /**
     * Update admin bar menu.
     * 
     * @since   1.0.1
     * @param   array   $admin_bar_menu     Admin bar menu to update.
     * @return  boolean                     Returns true if updated. Otherwise false.
     */
    public static function update_admin_bar($admin_bar)
    {
        $updated = false;

        if (is_array($admin_bar) && !empty($admin_bar))
            $updated =  update_option(Hide_Dashboard_Menu_Items_Config::admin_bar_option(), $admin_bar);

        return $updated;
    }

    /**
     * Update admin bar menu children.
     * 
     * @since   1.0.1
     * @param   array   $admin_bar_children  Children items to update.
     * @return  boolean                      Returns true if updated. Otherwise false.
     */
    public static function update_admin_bar_children($admin_bar_children)
    {
        $updated = false;

        if (is_array($admin_bar_children) && !empty($admin_bar_children))
            $updated =  update_option(Hide_Dashboard_Menu_Items_Config::admin_bar_children_option(), $admin_bar_children);

        return $updated;
    }

    /**
     * Update restricted admin bar menu.
     * 
     * @since   1.0.1
     * @param   array   $restricted_admin_bar  updated menu.
     * @return  boolean                             Returns true if updated. Otherwise false.
     */
    public static function update_restricted_admin_bar($restricted_admin_bar)
    {
        return self::update_plugin_setting(Hide_Dashboard_Menu_Items_Config::restricted_admin_bar_key(), $restricted_admin_bar);
    }

    /**
     * Update debug log entry.
     * 
     * @since   1.0.1
     * @param   array   $key        Key to find the correct entry to update.
     * @param   array   $message    New value.
     * @return  boolean             Returns true if updated. Otherwise false.
     */
    public static function update_debug_log($key, $message)
    {
        $updated = false;

        if (!($key && $message)) return $updated;

        $debug_data = self::get_debug_log();
        $debug_data[$key] = $message;
        $updated =  update_option(Hide_Dashboard_Menu_Items_Config::debug_option(), $debug_data);

        return $updated;
    }

    /**
     * Update error log entry.
     * 
     * @since   1.0.1
     * @param   array   $key        Key to find the correct entry to update.
     * @param   array   $message    New value.
     * @return  boolean             Returns true if updated. Otherwise false.
     */
    public static function update_error_log($message)
    {
        $updated = false;

        if (!$message) return $updated;

        // current data
        $key = current_time("Y-m-d H:i");

        $error_log = self::get_error_log();
        $error_log[$key] = $message;

        $updated =  update_option(Hide_Dashboard_Menu_Items_Config::error_option(), $error_log);

        return $updated;
    }

    /* --------------------------------------------------
        Add data using keys
    ----------------------------------------------------- */
    /**
     * Set scan start transient.
     *
     * @since   1.0.1
     */
    public static function add_scan_started_transient()
    {
        set_transient('hdmi_scan_has_started', true, 60);
    }

    /* --------------------------------------------------
        Remove data using keys
    ----------------------------------------------------- */

    /**
     * Removes scan start transient.
     *
     * @since   1.0.1
     */
    public static function remove_scan_started_transient()
    {
        delete_transient('hdmi_scan_has_started');
    }
}
