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
        return  $this->storage_manager->get_bypass_param();
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
     * Handle bypass gateway form submission and allow access if valid.
     *
     * @since 1.0.0
     * @return void
     */
    public function handle_bypass_form_submission()
    {
        if (
            !isset($_POST['hdmi_bypass_submit']) ||
            !isset($_POST['hdmi_bypass_input']) ||
            !isset($_POST['_wpnonce'])
        ) {
            return;
        }

        // Sanitize
        $input_param = sanitize_text_field(wp_unslash($_POST['hdmi_bypass_input']));
        $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));

        // Verify nonce
        if (!wp_verify_nonce($nonce, 'hdmi_bypass_form')) {
            $this->notice_manager->add_notice('invalid_nonce', __('Security check failed.', 'hide-dashboard-menu-items'), 'error');
            return;
        }

        // Compare with stored bypass passcode
        $stored_param = $this->storage_manager->get_bypass_param();
        if (!$stored_param || $input_param !== $stored_param) {
            $this->notice_manager->add_notice('invalid_passcode', __('Incorrect bypass passcode.', 'hide-dashboard-menu-items'), 'error');
            return;
        }

        // Allow access: update session or static flag
        self::$allow_access = true;

        // Optionally log it
        $this->debugger->log_debug('Bypass Granted?:', 'Bypass access granted via form');

        $this->notice_manager->add_notice('bypass_success', 'Bypass granted!');
    }


    /**
     * Remove hidden dashboard menu items.
     *
     * @since   1.0.0
     */
    public function remove_hidden_dashboard_menu_items()
    {
        $hidden_dashboard_menu_items = $this->storage_manager->get_hidden_dashboard_menu();

        if ($this->is_scanning() || empty($hidden_dashboard_menu_items)) return;

        // hide the menu items
        foreach ($hidden_dashboard_menu_items as $slug) {
            remove_menu_page($slug);
        }
    }

    /**
     * Remove hidden admin bar menu items.
     * 
     * @since   1.0.0
     */
    public function remove_hidden_admin_bar_menu_items()
    {
        $hidden_admin_bar_menu_items = $this->storage_manager->get_hidden_admin_bar_menu();

        if ($this->is_scanning() || empty($hidden_admin_bar_menu_items)) return;

        // hide the menu items
        global $wp_admin_bar;
        foreach ($hidden_admin_bar_menu_items as $id) {
            $wp_admin_bar->remove_menu($id);
        }
    }

    /**
     * Check if the given page is in the hidden admin menu items.
     * 
     * @since   1.0.0
     * @param   string      $needle     Item to compare.
     * @param   WP_Screen   $needle     Wordpress screen object.    
     * @return  boolean                 Returns true if matches. Otherwise false.
     */
    private function accessing_same_menu($needle, $screen)
    {
        $screen_id = $screen->id;
        $screen_base = $screen->base;
        $screen_parent_base = isset($screen->parent_base) ? $screen->parent_base : $screen_id;
        $screen_parent_file = isset($screen->parent_file) ? $screen->parent_file : $screen_id;

        $possible_haystack = array($screen_id, $screen_base, $screen_parent_base, $screen_parent_file);

        return in_array($needle, $possible_haystack);
    }

    /**
     * Output access gateway markup.
     * 
     * @since   1.0.0
     * @return  mixed    Returns the markup of access gateway with nonce and everything.
     */
    public function render_access_gateway_markup()
    {
        echo '<div class="wrap">';
        echo '<h1>🔒 Access Restricted</h1>';
        echo '<p>This page is hidden. To continue, enter the correct passcode:</p>';

        echo '<form method="post">';
        wp_nonce_field('hdmi_inline_unlock', 'hdmi_unlock_nonce');
        echo '<input type="text" name="bypass_code" class="regular-text" placeholder="Enter passcode" required />';
        submit_button('Unlock Access');
        echo '</form>';
        echo '</div>';
    }

    /**
     * Restrict access to hidden menu items and display a gateway.
     *
     * @since   1.0.0
     * @param   WP_Screen   $screen     Wordpress screen object
     * @return  void
     */
    public function menu_access_gateway($screen)
    {
        // first guard close
        if (!is_admin() || self::$allow_access || $this->is_scanning()) return;

        $hidden_dashboard_menu = $this->storage_manager->get_hidden_dashboard_menu();
        $hidden_admin_bar_menu = $this->storage_manager->get_hidden_admin_bar_menu();

        // second guard close
        if (!($hidden_dashboard_menu && $hidden_admin_bar_menu)) return;

        $render_gateway = false;

        foreach ($hidden_dashboard_menu as $slug) {
            if (
                $this->accessing_same_menu($slug, $screen)
            ) {
                $render_gateway = true;
            }
        }

        foreach ($hidden_admin_bar_menu as $id) {
            if (
                $this->accessing_same_menu($id, $screen)
            )
                $render_gateway = true;
        }

        if ($render_gateway) {
            $this->render_access_gateway_markup();
            exit;
        }
    }
}
