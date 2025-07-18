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
     * @var     string  $option_name    Plugin option prefix.
     */
    public $option_name;

    /**
     * Option for settings group.
     * 
     * @since   1.0.0
     * @var     string  $option_group   Option for settings group.
     */
    public $option_group;

    /**
     * Option for settings.
     * 
     * @since   1.0.0
     * @var     string  $settings_option    Option for settings.
     */
    public $settings_option;

    /**
     * Option for admin menu.
     * 
     * @since   1.0.0
     * @var     string  $db_menu_option Option for admin menu.
     */
    public $db_menu_option;

    /**
     * Option for admin toolbar menu.
     * 
     * @since   1.0.0
     * @var     string $tb_menu_option  Option for admin toolbar menu.
     */
    public $tb_menu_option;

    /**
     * Option for debug data.
     * 
     * @since   1.0.0
     * @var     string  $debug_option   Option for debug data.
     */
    public $debug_option;

    /**
     * Option to check if the previous scan was success.
     * 
     * @since   1.0.0
     * @var     string  $scan_success_option    Option to check if the previous scan was success.
     */
    public $scan_success_option;

    /**
     * Slug for the settings page.
     * 
     * @since   1.0.0
     * @var     string  $settings_page_slug     Slug for the settings page.
     */
    public $settings_page_slug;

    /**
     * Slug for the debug page.
     * 
     * @since   1.0.0
     * @var     string  $debug_page_slug    Slug for the debug page.
     */
    public $debug_page_slug;

    /**
     * Key for hidden dashboard menu.
     * 
     * @since   1.0.0
     * @var     string  $hidden_db_menu_key     Key for hidden dashboard menu.
     */
    public $hidden_db_menu_key = 'hidden_db_menu';

    /**
     * Key for hidden admin bar menu.
     * 
     * @since   1.0.0
     * @var     string  $hidden_tb_menu_key     Key for hidden admin bar menu.
     */
    public $hidden_tb_menu_key = 'hidden_tb_menu';

    /**
     * Key for bypass enabled value.
     * 
     * @since   1.0.0
     * @var     string  $bypass_enabled_key     Key for bypass enabled value.
     */
    public $bypass_enabled_key = 'bypass_enabled';

    /**
     * Bypass query parameter
     * 
     * @since   1.0.0
     * @var     string  bypass_param_key    Bypass query parameter
     */
    public $bypass_param_key = 'bypass_param';

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
        $this->option_name = 'hdmi';
        $this->option_group = $this->option_name . '_group';
        $this->scan_success_option = $this->option_name . '_scan_completed';
        $this->settings_option = $this->option_name . '_settings';
        $this->db_menu_option = $this->option_name . '_db_cached';
        $this->tb_menu_option = $this->option_name . '_tb_cached';
        $this->debug_option = $this->option_name . '_debug';

        $this->settings_page_slug = $plugin_name . '-settings';
        $this->debug_page_slug = $plugin_name . '-debug';
    }
}
