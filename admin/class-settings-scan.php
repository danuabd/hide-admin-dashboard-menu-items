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
     * Process scanning for menu items.
     *
     * @since    1.0.0
     */
    public function scan()
    {

        if (
            isset($_POST['hdmi_scan_request']) &&
            current_user_can('manage_options') &&
            check_admin_referer('hdmi_scan_nonce_action', 'hdmi_scan_nonce_field')
        ) {

            $this->is_scanning = true;

            global $menu;

            if (!empty($menu) || is_array($menu)) $this->store_menu_items();

            global $wp_admin_bar;

            if (!empty($wp_admin_bar) || is_array($wp_admin_bar)) $this->store_toolbar_items($wp_admin_bar);

            // Store in DB
            update_option($this->scan_success_option, 1);
            $this->log_timed_info('Last Scan Time');

            // Redirect back with success transient
            $this->set_admin_notice('hdmi_settings_updated', __('Menu scan completed successfully.', 'hide-dashboard-menu-items'), 'success');
            wp_redirect(admin_url('admin.php?page=' . $this->settings_page_slug));
            exit;
        }
    }

    /**
     * Get the registered top-level admin menu items.
     *
     * @since    1.0.0
     */
    public function store_menu_items($menu)
    {


        global $menu;

        if (empty($menu) || !is_array($menu)) {
            $this->log_error('Menu is not initialized or empty.');
            return;
        }

        $menu_items = array();
        $hidden_menu_items = $this->get_plugin_option($this->hidden_db_menu_key, []);

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

            if (empty($slug) || empty($title) || empty($icon) || $slug === $this->settings_page_slug) {
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
            $this->log_error('No top-level menu items found.');
            return;
        }

        error_log(print_r($menu_items));

        update_option($this->db_menu_option, $menu_items);
    }

    /**
     * Get the registered top-level toolbar menu items.
     *
     * @since    1.0.0
     */
    public function store_toolbar_items($admin_bar)
    {
        if (!is_object($wp_admin_bar)) {
            $this->log_error('WP Admin Bar is not initialized.');
            return;
        }

        $nodes = $wp_admin_bar->get_nodes();

        if (empty($nodes)) {
            $this->log_error('No admin bar nodes found.');
            return;
        }

        $menu_items  = array();

        foreach ($nodes as $node) {
            if (empty($node->parent)) {

                $title = strip_tags($node->title);
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
            $this->log_error('No top-level admin bar menu items found.');
            return;
        }

        update_option($this->tb_menu_option, $menu_items);
    }
}
