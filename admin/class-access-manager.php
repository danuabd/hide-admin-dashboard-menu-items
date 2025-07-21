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
     * Holds config class instance.
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
     * Holds notices manager class instance.
     * 
     * @since   1.0.0
     * @access  protected
     * @var     Hide_Dashboard_Menu_Items_Notice_Manager    $notice_manager
     */
    private $notice_manager;

    /**
     * Holds permission to hidden menu items
     * 
     * @since 1.0.0
     * @var boolean 
     */
    private static $allow_access = false;

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
    }

    /**
     * Get bypass passcode from storage.
     * 
     * @since   1.0.0
     * @return  string|null   Return bypass parameter from storage. Otherwise null.
     */
    private function bypass_param()
    {
        if (!self::$allow_access) {
            self::$allow_access = $this->storage_manager->get_bypass_param();
        }

        return self::$allow_access;
    }

    /**
     * Check if a scan is running or not.
     * 
     * @since   1.0.0
     * @return  boolean   True if scan is running. Otherwise False.
     */
    private static function is_scanning()
    {
        return get_transient('hdmi_scan_running');
    }

    /**
     * Check if requirements are met to fulfil hide/restrict request
     * 
     * @since   1.0.0
     * @return  boolean     Whether the requirements are met. Conditions are:
     * 
     * 1 - bypass feature is active.
     * 
     * 2 - bypass param is set.
     * 
     * 3 - scanning is not running.
     * 
     * 4 - require data is not missing (menu items).
     */
    private function has_required()
    {
        return
            $this->storage_manager->is_bypass_active() &&
            $this->storage_manager->get_bypass_param();
    }

    /**
     * Do nonce verification and update bypass active status.
     * 
     * @since   1.0.0
     * @return  void  Set the self::$allow_access based on a comparison between user input query parameter and stored parameter (if set).
     */
    public function set_bypass_access()
    {
        // Step 1: Check if required parameters exist
        if (!isset($_GET[Hide_Dashboard_Menu_Items_Config::BYPASS_PASSCODE_KEY], $_GET['_wpnonce'])) return false;

        // Step 2: Sanitize the nonce value
        $nonce = sanitize_text_field(wp_unslash($_GET['_wpnonce']));

        //  Step 3: Verify the nonce
        $nonce_verify_result = wp_verify_nonce($nonce, 'hdmi_bypass_access');

        if ($nonce_verify_result) {

            $bypass_input_param = sanitize_text_field(wp_unslash($_GET[Hide_Dashboard_Menu_Items_Config::BYPASS_PASSCODE_KEY]));

            if ($bypass_input_param === self::bypass_param())
                self::$allow_access = true;
            else
                self::$allow_access = false;
        } else
            self::$allow_access = false;
    }

    /**
     * Hide Dashboard Menu items.
     *
     * @since   1.0.0
     */
    public function hide_dashboard_menu()
    {
        $hidden_dashboard_menu = $this->storage_manager->get_hidden_dashboard_menu();

        if (!self::has_required() || empty($hidden_dashboard_menu)) return;

        // has access - allow
        if (self::$allow_access) {

            // allow further access by modifying URLs
            $this->update_dashboard_menu($hidden_dashboard_menu, self::bypass_param());

            // don't hide
            return;
        }

        // hide the menu items
        foreach ($hidden_dashboard_menu as $slug) {
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
        $tb_hidden = $this->storage_manager->get_hidden_admin_bar_menu();

        if (!self::has_required() || empty($tb_hidden)) return;

        // Has access - update URLs
        if (self::$allow_access) {

            // allow further access by modifying URLs
            $this->update_admin_bar_menu($tb_hidden, self::bypass_param());

            // don't hide
            return;
        }

        // hide the menu items
        global $wp_admin_bar;
        foreach ($tb_hidden as $id) {
            $wp_admin_bar->remove_menu($id);
        }
    }

    /**
     * Append the bypass query parameter to dashboard menu item URLs.
     *
     * @since   1.0.0
     * @param   array   $hidden_db The hidden menu slugs.
     * @param   string  $bypass_key The bypass parameter.
     */
    public function update_dashboard_menu($hidden_db, $bypass_key)
    {
        global $menu;
        $nonce = wp_create_nonce('hdmi_bypass_access');

        foreach ($menu as $index => $menu_item) {
            if (in_array($menu_item[2], $hidden_db, true)) {
                if (strpos($menu[$index][2], $bypass_key) === false) {
                    if (strpos($menu[$index][2], '?') !== false) {
                        $menu[$index][2] .= '&' . $this->config::OPTION_NAME . '=' . $bypass_key . '&_wpnonce=' . $nonce;
                    } else {
                        $menu[$index][2] .= '?' . $this->config::OPTION_NAME . '=' . $bypass_key . '&_wpnonce=' . $nonce;
                    }
                }
            }
        }

        $this->debugger->log_debug('Dashboard menu updated?', 'Yes');
        $this->debugger->log_debug('Dashboard menu was updated at', current_time('sql'));
    }


    /**
     * Append the bypass query parameter to admin bar menu item URLs.
     *
     * @since   1.0.0
     * @param   array   $hidden_tb      Hidden admin bat menu items.
     * @param   string  $bypass_key     User-set bypass parameter.
     */
    public function update_admin_bar_menu($hidden_tb, $bypass_key)
    {
        global $wp_admin_bar;

        $nonce = wp_create_nonce('hdmi_bypass_access');
        foreach ($hidden_tb as $slug) {
            $node = $wp_admin_bar->get_node($slug);

            if ($node && isset($node->href)) {
                if (strpos($node->href, $bypass_key) === false) {
                    $updated_href = add_query_arg($this->config::OPTION_NAME, $bypass_key, $nonce, $node->href);
                    $node->href = $updated_href;
                    $wp_admin_bar->add_menu($node);
                }
            }
        }

        $this->debugger->log_debug('Admin bar menu updated?', 'Yes');
        $this->debugger->log_debug('Admin bar menu was updated at', current_time('sql'));
    }

    /**
     * Function to restrict access to hidden menu items.
     *
     * @since   1.0.0
     * @return  void
     */
    public function restrict_menu_access()
    {
        $hidden_dashboard_menu = $this->storage_manager->get_hidden_dashboard_menu();
        $hidden_tb_menu = $this->storage_manager->get_hidden_admin_bar_menu();

        if (!self::has_required() || !(empty($hidden_dashboard_menu) && empty($hidden_tb_menu))) return;

        $hidden_all = array_merge($hidden_dashboard_menu, $hidden_tb_menu);

        $current_screen = get_current_screen();

        foreach ($hidden_all as $slug) {
            if (
                $current_screen === $slug
            ) {
                // allow access
                if (self::$allow_access) {
                    $this->notice_manager->add_notice('bypass_enabled', __('Bypass is active.', 'hide-dashboard-menu-items'), 'info');
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
