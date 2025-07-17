<?php

/**
 * Access manager class for the plugin
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
    public $plugin_name;
    public $version;
    public $config_path;
    public $option_name;
    public $option_group;
    public $settings_option;
    public $db_menu_option;
    public $tb_menu_option;
    public $debug_option;
    public $scan_success_option;
    public $settings_page_slug;
    public $debug_page_slug;
    public $hidden_db_menu_key = 'hidden_db_menu';
    public $hidden_tb_menu_key = 'hidden_tb_menu';
    public $bypass_enabled_key = 'bypass_enabled';
    public $bypass_param_key = 'bypass_param';

    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
        $this->config_path = plugin_dir_path(__FILE__);
        $this->option_name = 'hdmi';
        $this->option_group = $this->option_name . '_group';
        $this->settings_option = 'hdmi_settings';
        $this->db_menu_option = 'hdmi_db_cached';
        $this->tb_menu_option = 'hdmi_tb_cached';
        $this->scan_success_option = 'hdmi_scan_completed';
        $this->debug_option = 'hdmi_debug';

        $this->settings_page_slug = $plugin_name . '-settings';
        $this->debug_page_slug = $plugin_name . '-debug';
    }
}
