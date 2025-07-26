<?php

/**
 * Helper class for the plugin.
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.1
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/helper
 */
if (!defined('ABSPATH')) {
    echo 'Hi there!  I\'m just a plugin, not much I can do when called directly :).';
    exit;
}

class Hide_Dashboard_Menu_Items_Config
{

    /**
     * Plugin Name.
     * 
     * @since   1.0.0
     * @return  string    Plugin Name.
     */
    private static $plugin_name = HDMI_PLUGIN_NAME;

    /**
     * Plugin option prefix.
     * 
     * @since   1.0.0
     * @return  string    Plugin option prefix.
     * 
     */
    private static $option_prefix = HDMI_PREFIX;

    /**
     * Settings page slug.
     * 
     * @since   1.0.0
     * @return  string     Settings page slug.
     */
    public static function settings_slug()
    {
        return self::$plugin_name . '-settings';
    }

    /**
     * Debug page Slug.
     * 
     * @since   1.0.0
     * @return  string    Debug page Slug.
     */
    public static function debug_slug()
    {
        return self::$plugin_name . '-debug';
    }

    /**
     * Restricted page slug.
     * 
     * @since   1.0.0
     * @return  string    Restricted page slug.
     */
    public static function restricted_slug()
    {
        return self::$plugin_name . '-restricted';
    }

    /**
     * Option group.
     * 
     * @since   1.0.0
     * @return  string   Option group.
     */
    public static function option_group()
    {
        return self::$option_prefix . '_group';
    }

    /**
     * Settings option.
     * 
     * @since   1.0.0
     * @return  string    Settings option.
     */
    public static function settings_option()
    {
        return self::$option_prefix . '_settings';
    }

    /**
     * Dashboard menu option.
     * 
     * @since   1.0.0
     * @return  string    Admin menu option.
     */
    public static function dashboard_option()
    {
        return self::$option_prefix . '_dashboard';
    }

    /**
     * Dashboard menu option.
     * 
     * @since   1.0.0
     * @return  string    Admin menu option.
     */
    public static function dashboard_children_option()
    {
        return self::$option_prefix . '_dashboard_children';
    }

    /**
     * Admin bar menu option.
     * 
     * @since   1.0.0
     * @return  string    Admin bar menu option.
     */
    public static function admin_bar_option()
    {
        return self::$option_prefix . '_admin_bar';
    }

    /**
     * Admin bar menu option.
     * 
     * @since   1.0.0
     * @return  string    Admin bar menu option.
     */
    public static function admin_bar_children_option()
    {
        return self::$option_prefix . '_admin_bar_children';
    }

    /**
     * Debug option.
     * 
     * @since   1.0.0
     * @return  string   Debug option.
     */
    public static function debug_option()
    {
        return self::$option_prefix . '_debug';
    }

    /**
     * Error option.
     * 
     * @since   1.0.0
     * @return  string   Error option.
     */
    public static function error_option()
    {
        return self::$option_prefix . '_errors';
    }

    /**
     * Scan success option.
     * 
     * @since   1.0.0
     * @return  string    Scan success option.
     */
    public static function scan_success_option()
    {
        return self::$option_prefix . '_scan_success';
    }

    /**
     * Transient key scan running.
     * 
     * @since   1.0.1
     * @return  string    Transient key scan running.
     */
    public static function scan_running_transient()
    {
        return self::$option_prefix . '_tr_scan_running';
    }

    /**
     * Transient key scan completed.
     * 
     * @since   1.0.1
     * @return  string    Transient key scan completed.
     */
    public static function scan_completed_transient()
    {
        return self::$option_prefix . '_tr_scan_completed';
    }

    /**
     * Hidden dashboard key.
     * 
     * @since   1.0.0
     * @return  string     Hidden dashboard key.
     */
    public static function hidden_dashboard_key()
    {
        return 'hidden_dashboard';
    }

    /**
     * Restricted dashboard key.
     * 
     * @since   1.0.0
     * @return  string     Restricted dashboard key.
     */
    public static function restricted_dashboard_key()
    {
        return 'restricted_dashboard';
    }

    /**
     * Hidden admin bar key.
     * 
     * @since   1.0.1
     * @return  string     Hidden admin bar key.
     */
    public static function hidden_admin_bar_key()
    {
        return 'hidden_admin_bar';
    }

    /**
     * Restricted admin bar Restricted admin bar key.
     * 
     * @since   1.0.1
     * @return  string     Restricted admin bar key.
     */
    public static function restricted_admin_bar_key()
    {
        return 'restricted_admin_bar';
    }

    /**
     * Restriction status key.
     * 
     * @since   1.0.1
     * @return  string     Restriction status key.
     */
    public static function restrict_status_key()
    {
        return 'restrict_status';
    }

    /**
     * Bypass status key.
     * 
     * @since   1.0.0
     * @return  string     Bypass status key.
     */
    public static function bypass_status_key()
    {
        return 'bypass_status';
    }

    /**
     * Bypass passcode key.
     * 
     * @since   1.0.0
     * @return  string    Bypass passcode key.
     */
    public static function bypass_passcode_key()
    {
        return 'bypass_passcode';
    }
}
