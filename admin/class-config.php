<?php

/**
 * Config class for the plugin.
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

class Hide_Dashboard_Menu_Items_Config
{

    /**
     * Name of the plugin
     * 
     * @since   1.0.0
     * @var     $plugin_name    Name of the plugin
     */
    public $plugin_name;

    /**
     * Version of the plugin
     * 
     * @since   1.0.0
     * @var     $version    Version of the plugin
     * 
     */
    public $version;

    /**
     * Plugin option prefix.
     * 
     * @since   1.0.0
     * @var     string  OPTION_NAME    Plugin option prefix.
     * 
     */
    public const OPTION_NAME = 'hdmi';

    /**
     * Option for settings group.
     * 
     * @since   1.0.0
     * @var     string  OPTION_GROUP   Option for settings group.
     */
    public const OPTION_GROUP = self::OPTION_NAME . '_group';

    /**
     * Option for settings.
     * 
     * @since   1.0.0
     * @var     string  SETTINGS_OPTION    Option for settings.
     */
    public const SETTINGS_OPTION = self::OPTION_NAME . '_settings';

    /**
     * Option for admin menu.
     * 
     * @since   1.0.0
     * @var     string  DASHBOARD_MENU_OPTION Option for admin menu.
     */
    public const DASHBOARD_MENU_OPTION = self::OPTION_NAME . '_dashboard_menu';

    /**
     * Option for admin toolbar menu.
     * 
     * @since   1.0.0
     * @var     string ADMIN_BAR_MENU_OPTION  Option for admin toolbar menu.
     */
    public const ADMIN_BAR_MENU_OPTION = self::OPTION_NAME . '_admin_bar_menu';

    /**
     * Option for debug data.
     * 
     * @since   1.0.0
     * @var     string  DEBUG_LOG_OPTION   Option for debug data.
     */
    public const DEBUG_LOG_OPTION = self::OPTION_NAME . '_debug_log';

    /**
     * Option to check if the previous scan was success.
     * 
     * @since   1.0.0
     * @var     string  SCAN_SUCCESS_OPTION    Option to check if the previous scan was success.
     */
    public const SCAN_SUCCESS_OPTION = self::OPTION_NAME . '_scan_success';

    /**
     * Slug for the settings page.
     * 
     * @since   1.0.0
     * @var     string  SETTINGS_PAGE_SLUG     Slug for the settings page.
     */
    public const SETTINGS_PAGE_SLUG = $this->plugin_name . '-settings';

    /**
     * Slug for the debug page.
     * 
     * @since   1.0.0
     * @var     string  DEBUG_PAGE_SLUG    Slug for the debug page.
     */
    public const DEBUG_PAGE_SLUG = $this->plugin_name . '-debug';

    /**
     * Key for hidden dashboard menu.
     * 
     * @since   1.0.0
     * @var     string  HIDDEN_DASHBOARD_MENU_KEY     Key for hidden dashboard menu.
     */
    public const HIDDEN_DASHBOARD_MENU_KEY = 'hidden_dashboard_menu';

    /**
     * Key for hidden admin bar menu.
     * 
     * @since   1.0.0
     * @var     string  HIDDEN_ADMIN_BAR_MENU     Key for hidden admin bar menu.
     */
    public const HIDDEN_ADMIN_BAR_MENU = 'hidden_admin_bar_menu';

    /**
     * Key for bypass enabled value.
     * 
     * @since   1.0.0
     * @var     string  BYPASS_STATUS_KEY     Key for bypass enabled value.
     */
    public const BYPASS_STATUS_KEY = 'bypass_status';

    /**
     * Bypass query parameter
     * 
     * @since   1.0.0
     * @var     string  BYPASS_PASSCODE    Bypass query parameter
     */
    public const BYPASS_PASSCODE = 'bypass_passcode';

    /**
     * Build plugin constants
     * 
     * @since   1.0.0
     * @param   string  $plugin_name    Name of the plugin
     * @param   string  $version        Plugin version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }
}
