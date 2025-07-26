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
     * @param   Hide_Dashboard_Menu_Items_Debugger          $debugger
     * @param   Hide_Dashboard_Menu_Items_Notice_Manager    $notice_manager
     */
    public function __construct(
        Hide_Dashboard_Menu_Items_Debugger $debugger,
        Hide_Dashboard_Menu_Items_Notice_Manager $notice_manager
    ) {
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
        static $has_run = false;

        if ($has_run) return;
        $has_run = true;

        register_setting(
            Hide_Dashboard_Menu_Items_Config::option_group(),
            Hide_Dashboard_Menu_Items_Config::settings_option(),
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
        static $has_run = false;

        if ($has_run) return;
        $has_run = true;

        // Add a new top-level menu item.
        $this->settings_page_hook_suffix =  add_menu_page(
            'Configure Hide Menu Items',
            __('Hide Menu Items', 'hide-dashboard-menu-items'),
            'manage_options',
            Hide_Dashboard_Menu_Items_Config::settings_slug(),
            array($this, 'render_settings_page'),
            'dashicons-hidden',
            99
        );

        $this->debug_page_hook_suffix = add_submenu_page(
            Hide_Dashboard_Menu_Items_Config::settings_slug(),
            __('Debug Info', 'hide-dashboard-menu-items'),
            __('Debug Info', 'hide-dashboard-menu-items'),
            'manage_options',
            Hide_Dashboard_Menu_Items_Config::debug_slug(),
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
            constant('HDMI_PLUGIN_NAME') . '_settings_section',
            '',
            '__return_false',
            Hide_Dashboard_Menu_Items_Config::settings_slug()
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

        $settings_page_slug = Hide_Dashboard_Menu_Items_Config::settings_slug();
        $scan_completed = Hide_Dashboard_Menu_Items_Storage_Manager::get_scan_status_cache();
        $hidden_dashboard = Hide_Dashboard_Menu_Items_Storage_Manager::get_hidden_dashboard();
        $hidden_admin_bar = Hide_Dashboard_Menu_Items_Storage_Manager::get_hidden_admin_bar();
        $dashboard = Hide_Dashboard_Menu_Items_Storage_Manager::get_dashboard_cache();
        $admin_bar = Hide_Dashboard_Menu_Items_Storage_Manager::get_admin_bar_cache();
        $is_restrict_enabled = Hide_Dashboard_Menu_Items_Storage_Manager::is_restrict_active();
        $is_bypass_enabled = Hide_Dashboard_Menu_Items_Storage_Manager::is_bypass_active();
        $bypass_code = Hide_Dashboard_Menu_Items_Storage_Manager::get_bypass_code();

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
        $this->debugger->log_debug('Settings form last submitted', current_time('Y-M-D H:i'));

        if (!is_array($user_input || empty($user_input))) {
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
            $this->debugger->log_debug('Settings last updated', current_time('Y-M-D H:i'));
        }

        $this->notice_manager->add_plugin_notice(__('Settings have been updated.', 'hide-dashboard-menu-items'), 'success');

        return $sanitized;
    }

    /**
     * Store hidden menu children in storage
     *
     * @since   1.0.1
     * @param   array $old_value Old value
     * @param   array $new_value New value
     */
    public function collect_hidden_menu_children($old_value, $new_value)
    {
        static $has_run = false;

        if ($has_run) return;
        $has_run = true;

        $dashboard_key = Hide_Dashboard_Menu_Items_Config::hidden_dashboard_key();
        $admin_bar_key = Hide_Dashboard_Menu_Items_Config::hidden_admin_bar_key();

        // Extract menu values
        $old_dashboard = $old_value[$dashboard_key] ?? [];
        $old_admin_bar = $old_value[$admin_bar_key] ?? [];

        $new_dashboard = $new_value[$dashboard_key] ?? [];
        $new_admin_bar = $new_value[$admin_bar_key] ?? [];

        $dashboard_changed = $old_dashboard === $new_dashboard;
        $admin_bar_changed = $old_admin_bar === $new_admin_bar;

        if (!$dashboard_changed && !$admin_bar_changed) {
            return;
        }

        error_log('collect_hidden_menu_children executing');

        // Handle dashboard menu
        if (!empty($new_dashboard) && isset($GLOBALS['menu'])) {
            global $submenu;

            $restricted_dashboard_links = [];

            foreach ($new_dashboard as $parent_slug) {
                $restricted_dashboard_links[] = $parent_slug;

                if (!empty($submenu[$parent_slug])) {
                    foreach ($submenu[$parent_slug] as $item) {
                        if (isset($item[2])) {
                            $restricted_dashboard_links[] = $item[2];
                        }
                    }
                }
            }

            Hide_Dashboard_Menu_Items_Storage_Manager::update_restricted_dashboard($restricted_dashboard_links);
        }

        // Handle admin bar menu
        if (!empty($new_admin_bar) && isset($GLOBALS['wp_admin_bar'])) {
            global $wp_admin_bar;

            $children_cache = Hide_Dashboard_Menu_Items_Storage_Manager::get_admin_bar_children_cache();
            if (empty($children_cache)) return;

            $restricted_admin_links = [];

            foreach ($new_admin_bar as $id) {
                $node = $wp_admin_bar->get_node($id);
                if (!empty($node->href)) {
                    $restricted_admin_links[] = $node->id;
                }

                if (!empty($children_cache[$id])) {
                    foreach ($children_cache[$id] as $child) {
                        if (isset($child['id'])) {
                            $restricted_admin_links[] = $child['id'];
                        }
                    }
                }
            }

            Hide_Dashboard_Menu_Items_Storage_Manager::update_restricted_admin_bar($restricted_admin_links);
        }
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
            wp_enqueue_style(Hide_Dashboard_Menu_Items_Config::settings_slug(), $css_base_url . 'admin.css', array(), filemtime($css_base_path . 'admin.css'), 'all');
        }

        // load styles only in plugin debug page
        if ($hook_suffix === $this->debug_page_hook_suffix) {
            wp_enqueue_style(Hide_Dashboard_Menu_Items_Config::debug_slug(), $css_base_url . 'debug.css', array(), filemtime($css_base_path . 'debug.css'), 'all');
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

        wp_enqueue_script(constant('HDMI_PLUGIN_NAME'), plugin_dir_url(__FILE__) . 'js/hide-dashboard-menu-items-admin.js', array(), filemtime(plugin_dir_path(__FILE__) . 'js/hide-dashboard-menu-items-admin.js'), false);
    }
}
