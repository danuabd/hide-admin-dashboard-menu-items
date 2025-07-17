<?php

/**
 * Settings class for the plugin
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
    private $config;

    private $option_manager;

    private $debugger;

    private $notices;

    private $settings_page_hook_suffix;

    private $debug_page_hook_suffix;

    public function __construct(
        Hide_Dashboard_Menu_Items_Config $config,
        Hide_Dashboard_Menu_Items_Options $option_manager,
        Hide_Dashboard_Menu_Items_Debugger $debugger,
        Hide_Dashboard_Menu_Items_Notices $notices
    ) {
        $this->config = $config;
        $this->option_manager = $option_manager;
        $this->debugger = $debugger;
        $this->notices = $notices;
    }

    /**
     * Register settings for this plugin
     * 
     * @since 1.0.0
     */
    public function register_settings()
    {

        register_setting(
            $this->config->option_group,
            $this->config->settings_option,
            array($this, 'sanitize_submissions')
        );
    }

    /**
     * Register the admin menu for this plugin (dashboard area).
     *
     * @since    1.0.0
     */
    public function add_admin_menu()
    {
        // Add a new top-level menu item.
        $this->settings_page_hook_suffix =    add_menu_page(
            'Configure Hide Menu Items',
            __('Hide Menu Items', $this->config->plugin_name),
            'manage_options',
            $this->config->settings_page_slug,
            array($this, 'display_settings_page'),
            'dashicons-hidden',
            99
        );

        $this->debug_page_hook_suffix = add_submenu_page(
            $this->config->settings_page_slug,
            __('Debug Info', $this->config->plugin_name),
            __('Debug Info', $this->config->plugin_name),
            'manage_options',
            $this->config->debug_page_slug,
            [$this, 'display_debug_page']
        );
    }



    /**
     * Register the settings fields and sections for this plugin.
     *
     * @since    1.0.0
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
     * @since    1.0.0
     */
    public function render_settings_page()
    {
        // Check if the user has the required capability.
        if (!current_user_can('manage_options')) {
            return;
        }

        $scan_done = get_option($this->config->scan_success_option, false);
        $bypass_enabled_key = $this->config->bypass_enabled_key;
        $hidden_db_menu_key = $this->config->hidden_db_menu_key;
        $hidden_tb_menu_key = $this->config->hidden_tb_menu_key;
        $settings_option = $this->config->settings_option;
        $bypass_param_key = $this->config->bypass_param_key;
        $option_group = $this->config->option_group;
        $settings_page_slug = $this->config->settings_page_slug;

        // Cached menu items
        $cached_db_menu = get_option($this->config->db_menu_option, array());
        $cached_tb_menu = get_option($this->config->tb_menu_option, array());

        // Hidden menu items
        $hidden_db_menu =
            $this->option_manager->get($this->config->hidden_db_menu_key, array());
        $hidden_tb_menu =
            $this->option_manager->get($this->config->hidden_tb_menu_key, array());

        $bypass_enabled = $this->option_manager->is_bypass_active($bypass_enabled_key)  ? 'checked' : '';
        $bypass_value =
            esc_attr($this->option_manager->get_bypass_param($bypass_enabled_key, $bypass_param_key)) ?? '';


        // Include the settings page template.
        include_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-admin-display.php';
    }

    /**
     * Sanitize user inputs
     * 
     * @since 1.0.0
     * 
     * @param array $input User inputs received via admin form
     * @return array Sanitized array of options
     */
    public function sanitize_settings($input)
    {
        $this->debugger->log_event('Settings form last submitted');

        if (!is_array($input)) {
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
            // You can choose to filter out other types (objects, etc.) or keep them
            return '';
        };

        $sanitized = array_map($sanitize_recursive, $input);

        if (!empty($sanitized)) {
            $this->debugger->log_event('Settings last updated');
        }

        $this->notices->add_notice('hdmi_settings_updated', __('Settings have been updated.', 'hide-dashboard-menu-items'), 'success');
        return $sanitized;
    }

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles($hook_suffix)
    {
        $css_base_url = plugin_dir_url(__FILE__) . 'css/hide-dashboard-menu-items-';
        $css_base_path = plugin_dir_path(__FILE__) . 'css/hide-dashboard-menu-items-';

        // load styles only in plugin admin settings page
        if ($hook_suffix === $this->settings_page_hook_suffix || $hook_suffix === $this->debug_page_hook_suffix) {
            wp_enqueue_style($this->config->settings_page_slug, $css_base_url . 'admin.css', array(), filemtime($css_base_path . 'admin.css'), 'all');
        }

        if ($hook_suffix === $this->debug_page_hook_suffix) {
            wp_enqueue_style($this->config->debug_page_slug, $css_base_url . 'debug.css', array(), filemtime($css_base_path . 'debug.css'), 'all');
        }
    }

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
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
