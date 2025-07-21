<?php

/**
 * Scanner class for the plugin
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
class Hide_Dashboard_Menu_Items_Scanner
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
     * @var     Hide_Dashboard_Menu_Items_Debugger      $debugger
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
     * Start scanning menu items.
     *
     * @since   1.0.0
     */
    public function scan()
    {
        if (
            isset($_POST['hdmi_scan_request']) &&
            current_user_can('manage_options') &&
            check_admin_referer('hdmi_scan_nonce_action', 'hdmi_scan_nonce_field')
        ) {
            add_action('admin_menu', [$this, 'store_dashboard_menu'], 999);
            do_action('admin_menu', $GLOBALS['menu']);

            add_action('admin_bar_menu', [$this, 'store_admin_bar_menu'], 999);
            do_action('admin_bar_menu', $GLOBALS['wp_admin_bar']);

            update_option($this->config::SCAN_SUCCESS_OPTION, 1);
            $this->debugger->log_debug('Last Scan Time', current_time('sql'));

            // Redirect back with success transient
            $this->notice_manager->add_notice('scan_completed', __('Menu scan completed successfully.', 'hide-dashboard-menu-items'), 'success');

            set_transient('scan_is_completed', 30);
            wp_redirect(admin_url('admin.php?page=' . $this->config->settings_page_slug));
            exit;
        }
    }

    /**
     * Get the registered top-level admin menu items.
     *
     * @since   1.0.0
     * @param   object  $menu Dashboard menu
     */
    public function store_dashboard_menu($menu)
    {

        if (empty($menu) || !is_array($menu)) {
            $this->debugger->log_error('Menu is not initialized or empty.');
            return;
        }

        $menu_items = array();
        $hidden_menu_items = $this->storage_manager->get_hidden_dashboard_menu();

        $menu_combined = array_merge($menu, $hidden_menu_items);

        foreach ($menu_combined as $item) {
            $raw_title = isset($item[0]) ? $item[0] : '';

            // extract clean title
            if (strpos($raw_title, '<') !== false) {
                // Contains HTML, extract only the part before it
                preg_match('/^[^<]+/', $raw_title, $matches);
                $title = isset($matches[0]) ? trim($matches[0]) : '';
            } else {
                // No HTML, use as-is
                $title = trim($raw_title);
            }

            $capability = isset($item[1]) ? $item[1] : '';
            $slug       = isset($item[2]) ? $item[2] : '';
            $icon       = isset($item[6]) ? $item[6] : '';

            if (empty($slug) || empty($title) || empty($icon) || $slug === $this->config->settings_page_slug) {
                // Skip items with missing data
                continue;
            }

            if (current_user_can($capability)) {
                $menu_items[] = array(
                    'slug'     => $slug,
                    'title'    => $title,
                    'dashicon' => $icon,
                );
            }
        }

        if (empty($menu_items)) {
            $this->debugger->log_error('No top-level menu items found.');
            return;
        }

        $this->storage_manager->update_dashboard_menu($menu_items);
    }

    /**
     * Get the registered top-level admin bar menu items.
     *
     * @since   1.0.0
     * @param   WP_ADMIN_BAR $wp_admin_bar      Admin bar (toolbar) menu
     */
    public function store_admin_bar_menu($wp_admin_bar)
    {

        if (!($wp_admin_bar instanceof WP_Admin_Bar)) {
            $this->debugger->log_error('WP Admin Bar is not initialized.');
            return;
        }

        $nodes = $wp_admin_bar->get_nodes();

        if (empty($nodes)) {
            $this->debugger->log_error('No admin bar nodes found.');
            return;
        }

        $menu_items  = array();

        foreach ($nodes as $node) {
            if (empty($node->parent)) {

                $title = wp_strip_all_tags($node->title);
                if (empty($title) || $node->href === get_site_url() || $node->id === 'site-name') {
                    continue;
                }

                $title = strpos($title, 'Comments') !== false ? 'Comments' : $title;

                $menu_items[] = array(
                    'id'    => $node->id,
                    'title' => $title,
                );
            }
        }

        if (empty($menu_items)) {
            $this->debugger->log_error('No top-level admin bar menu items found.');
            return;
        }

        $this->storage_manager->update_admin_bar_menu($menu_items);
    }
}
