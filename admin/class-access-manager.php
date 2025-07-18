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
class Hide_Dashboard_Menu_Items_Access_Manager
{
    /**
     * Config data
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Config    $config
     */
    private $config;

    /**
     * Storage manager
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     */
    private $storage_manager;

    /**
     * Storage manager
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Debugger  $debugger
     */
    private $debugger;

    /**
     * Admin notices manager
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Notice_Manager    $notice_manager
     */
    private $notice_manager;

    /**
     * Bypass query parameter
     * 
     * @since   1.0.0
     * @access  protected
     * @var     string  $bypass_param_query
     */
    private $bypass_param_query;

    /**
     * Initialize the class and set its properties.
     * 
     * @since   1.0.0
     * @param   Hide_Dashboard_Menu_Items_Config    $config
     * @param   Hide_Dashboard_Menu_Items_Storage_Manager   $storage_manager
     * @param   Hide_Dashboard_Menu_Items_Debugger  $debugger
     * @param   Hide_Dashboard_Menu_Items_Notices   $notice_manager
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
        $this->bypass_param_query = $this->config->option_name;
    }

    /**
     * Hide Dashboard Menu items.
     *
     * @since   1.0.0
     */
    public function hide_dashboard_menu()
    {
        $scan_running = get_transient('hdmi_scan_running');

        if ($scan_running) return;

        $db_hidden = $this->storage_manager->get_hidden_db_menu();

        $bypass_active = $this->storage_manager->is_bypass_active();

        $bypass_param = $this->storage_manager->get_bypass_param();

        $bypass_param_in_uri = isset($_GET[$this->bypass_param_query]) && sanitize_text_field(wp_unslash($_GET[$this->bypass_param_query]))  === $bypass_param;

        if (!is_array($db_hidden) || empty($db_hidden)) {
            return;
        }

        if ($bypass_active && $bypass_param_in_uri) {
            $this->debugger->log_event('Bypass Active', 'Yes');
            $this->notice_manager->add_notice('bypass_enabled', __('Bypass is active and has been accessed', 'hide-dashboard-menu-items'), 'info');
            $this->update_dashboard_menu($db_hidden, $bypass_param);
            return;
        }

        // No access — remove the menu items
        foreach ($db_hidden as $slug) {
            remove_menu_page($slug);
        }
    }

    /**
     * Hide Admin Toolbar items.
     * 
     * @since   1.0.0
     */
    public function hide_toolbar_menu()
    {
        $scan_running = get_transient('hdmi_scan_running');

        if ($scan_running) return;

        global $wp_admin_bar;

        $tb_hidden = $this->storage_manager->get_hidden_tb_menu();

        $bypass_active =
            $this->storage_manager->is_bypass_active();
        $bypass_param =
            $this->storage_manager->get_bypass_param();

        $bypass_param_in_uri = isset($_GET[$this->bypass_param_query]) && sanitize_text_field(wp_unslash($_GET[$this->bypass_param_query]))  === $bypass_param;

        if (!is_array($tb_hidden) || empty($tb_hidden)) {
            return;
        }

        if ($bypass_active && $bypass_param_in_uri) {
            $this->debugger->log_event('Bypass Active', 'Yes');
            $this->notice_manager->add_notice('bypass_enabled', __('Bypass is active and has been accessed', 'hide-dashboard-menu-items'), 'info');
            $this->update_toolbar_menu($tb_hidden, $bypass_param);
            return;
        }

        // No access — remove the menu items
        foreach ($tb_hidden as $id) {
            $wp_admin_bar->remove_menu($id);
        }
    }

    /**
     * Append the bypass query parameter to dashboard menu item URLs.
     *
     * @since   1.0.0
     * @param   array   $hidden The hidden menu slugs.
     * @param   string  $bypass_key The bypass query key.
     */
    public function update_dashboard_menu($hidden, $bypass_key)
    {
        global $menu;

        foreach ($menu as $index => $menu_item) {
            if (in_array($menu_item[2], $hidden, true)) {
                if (strpos($menu[$index][2], $bypass_key) === false) {
                    if (strpos($menu[$index][2], '?') !== false) {
                        $menu[$index][2] .= '&' . $this->config->option_name . '=' . $bypass_key;
                    } else {
                        $menu[$index][2] .= '?' . $this->config->option_name . '=' . $bypass_key;
                    }
                }
            }
        }

        $this->debugger->log_event('Dashboard menu updated?', 'Yes');
        $this->debugger->log_event('Dashboard menu was updated at');
    }


    /**
     * Append the bypass query parameter to admin toolbar menu item URLs.
     *
     * @since   1.0.0
     * @param   array   $hidden_slugs
     * @param   string  $bypass_key
     */
    public function update_toolbar_menu($hidden_slugs, $bypass_key)
    {
        global $wp_admin_bar;

        foreach ($hidden_slugs as $slug) {
            $node = $wp_admin_bar->get_node($slug);

            if ($node && isset($node->href)) {
                if (strpos($node->href, $bypass_key) === false) {
                    $updated_href = add_query_arg($this->config->option_name, $bypass_key, $node->href);
                    $node->href = $updated_href;
                    $wp_admin_bar->add_menu($node);
                }
            }
        }

        $this->debugger->log_event('Admin bar menu updated?', 'Yes');
        $this->debugger->log_event('Admin bar menu was updated at');
    }

    /**
     * Function to restrict access to hidden menu items.
     *
     * @since   1.0.0
     */
    public function restrict_menu_access()
    {

        $hidden_db_menu = $this->storage_manager->get_hidden_db_menu();
        $hidden_tb_menu = $this->storage_manager->get_hidden_tb_menu();

        if (empty($hidden_db_menu) && empty($hidden_tb_menu)) {
            return;
        }

        $bypass_active = $this->storage_manager->is_bypass_active();
        $bypass_param = $this->storage_manager->get_bypass_param();
        $bypass_param_key = $this->config->option_name;

        $has_access = $bypass_active && isset($_GET[$bypass_param_key]) && sanitize_text_field(wp_unslash($_GET[$bypass_param_key])) === $bypass_param;

        $hidden_all = array_merge($hidden_db_menu, $hidden_tb_menu);

        $current_screen = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : basename($_SERVER['PHP_SELF']);

        foreach ($hidden_all as $slug) {
            if (
                strpos($_SERVER['REQUEST_URI'], $slug) !== false
                || $current_screen === $slug
            ) {
                // allow access
                if ($has_access) {
                    return;
                }

                // Restrict access
                status_header(403);
                nocache_headers();
                exit;
            }
        }
    }
}
