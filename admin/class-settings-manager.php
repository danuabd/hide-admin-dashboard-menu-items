<?php

/**
 * Admin Settings class for the plugin
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
class Hide_Dashboard_Menu_Items_Admin_Settings
{
    /**
     * Holds configuration class instance.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Config    $config
     */
    private $config;

    /**
     * Holds storage manager class instance.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     */
    private $storage_manager;

    /**
     * Holds debugger class instance.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Debugger  $debugger
     */
    private $debugger;

    /**
     * Holds notice manager class instance.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Notice_Manager    $notice_manager
     */
    private $notice_manager;

    /**
     * Settings page page hook suffix.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     string      $settings_page_hook_suffix
     */
    private $settings_page_hook_suffix;

    /**
     * Debug page hook suffix.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     string      $debug_page_hook_suffix
     */
    private $debug_page_hook_suffix;

    /**
     * Initialize class with required instances
     * 
     * @since   1.0.0
     * @param   Hide_Dashboard_Menu_Items_Config            $config
     * @param   Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     * @param   Hide_Dashboard_Menu_Items_Debugger          $debugger
     * @param   Hide_Dashboard_Menu_Items_Notice_Manager    $notice_manager
     */
    public function __construct(
        Hide_Dashboard_Menu_Items_Config $config,
        Hide_Dashboard_Menu_Items_Storage_Manager $storage_manager,
        Hide_Dashboard_Menu_Items_Debugger $debugger,
        Hide_Dashboard_Menu_Items_Notice_Manager $notice_manager
    ) {
        $this->config = $config;
        $this->storage_manager = $storage_manager;
        $this->debugger = $debugger;
        $this->notice_manager = $notice_manager;
    }

    /**
     * Register settings for this plugin
     * 
     * @since   1.0.0
     */
    public function register_settings()
    {

        register_setting(
            $this->config::OPTION_GROUP,
            $this->config::SETTINGS_OPTION,
            array($this, 'sanitize_submissions')
        );
    }

    /**
     * Register the admin menu for this plugin (dashboard area).
     *
     * @since   1.0.0
     */
    public function add_admin_menu()
    {
        // Add a new top-level menu item.
        $this->settings_page_hook_suffix =  add_menu_page(
            'Configure Hide Menu Items',
            __('Hide Menu Items', 'hide-dashboard-menu-items'),
            'manage_options',
            $this->config->settings_page_slug,
            array($this, 'render_settings_page'),
            'dashicons-hidden',
            99
        );

        $this->debug_page_hook_suffix = add_submenu_page(
            $this->config->settings_page_slug,
            __('Debug Info', 'hide-dashboard-menu-items'),
            __('Debug Info', 'hide-dashboard-menu-items'),
            'manage_options',
            $this->config->debug_page_slug,
            [$this->debugger, 'render_debug_page']
        );
    }



    /**
     * Register the settings fields and sections for this plugin.
     *
     * @since   1.0.0
     */
    public function register_fields_and_sections()
    {
        add_settings_section(
            $this->config->plugin_name . '_settings_section',
            '',
            '__return_false',
            $this->config->settings_page_slug
        );
    }

    /**
     * Register the settings page for this plugin.
     * 
     * @since   1.0.0
     */
    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $settings_page_slug = $this->config->settings_page_slug;
        $scan_completed = $this->storage_manager->get_scan_status_cache();
        $dashboard_menu = $this->storage_manager->get_dashboard_menu_cache();
        $admin_bar_menu = $this->storage_manager->get_admin_bar_menu_cache();
        $hidden_dashboard_menu = $this->storage_manager->get_hidden_dashboard_menu();
        $hidden_admin_bar_menu = $this->storage_manager->get_hidden_admin_bar_menu();
        $is_bypass_enabled = $this->storage_manager->is_bypass_active();
        $bypass_parameter = $this->storage_manager->get_bypass_param();

        require_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-admin-display.php';
    }

    /**
     * Sanitize user inputs
     * 
     * @since   1.0.0
     * @param   array   $input User     inputs received via admin form
     * @return  array   Sanitized array of options
     */
    public function sanitize_settings($user_input)
    {
        $this->debugger->log_debug('Settings form last submitted', current_time('sql'));

        if (!is_array($user_input)) {
            return [];
        }

        $sanitize_recursive = function ($value) use (&$sanitize_recursive) {
            if (is_array($value)) {
                return array_map($sanitize_recursive, $value);
            } elseif (is_bool($value)) {
                return $value;
            } elseif (is_string($value)) {
                return sanitize_text_field($value);
            }
            return '';
        };

        $sanitized = array_map($sanitize_recursive, $user_input);

        if (!empty($sanitized)) {
            $this->debugger->log_debug('Settings last updated', current_time('sql'));
        }

        $this->notice_manager->add_notice('settings_updated', __('Settings have been updated.', 'hide-dashboard-menu-items'), 'success');
        return $sanitized;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since   1.0.0
     * @param   string  $hook_suffix    current page slug
     */
    public function enqueue_styles($hook_suffix)
    {
        $css_base_url = plugin_dir_url(__FILE__) . 'css/hide-dashboard-menu-items-';
        $css_base_path = plugin_dir_path(__FILE__) . 'css/hide-dashboard-menu-items-';

        // load styles in plugin admin settings & debug page
        if ($hook_suffix === $this->settings_page_hook_suffix || $hook_suffix === $this->debug_page_hook_suffix) {
            wp_enqueue_style($this->config->settings_page_slug, $css_base_url . 'admin.css', array(), filemtime($css_base_path . 'admin.css'), 'all');
        }

        // load styles only in plugin debug page
        if ($hook_suffix === $this->debug_page_hook_suffix) {
            wp_enqueue_style($this->config->debug_page_slug, $css_base_url . 'debug.css', array(), filemtime($css_base_path . 'debug.css'), 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since   1.0.0
     * @param   string  $hook_suffix    current page slug
     */
    public function enqueue_scripts($hook_suffix)
    {
        // load script only in plugin admin settings page
        if ($hook_suffix !== $this->settings_page_hook_suffix) {
            return;
        }

        wp_enqueue_script($this->config->plugin_name, plugin_dir_url(__FILE__) . 'js/hide-dashboard-menu-items-admin.js', array(), plugin_dir_path(__FILE__) . 'js/hide-dashboard-menu-items-admin.js', false);
    }
}
