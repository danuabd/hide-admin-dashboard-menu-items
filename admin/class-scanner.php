<?php

/**
 * Scanner class for the plugin
 *
 * @link https://danukaprasad.com
 * @since 1.0.0
 * @package Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin
 */
if (!defined('ABSPATH')) {
    exit;
}

class Hide_Dashboard_Menu_Items_Scanner
{

    /**
     * @var Hide_Dashboard_Menu_Items_Debugger
     */
    private $debugger;

    /**
     * @var Hide_Dashboard_Menu_Items_Notice_Manager
     */
    private $notice_manager;

    /**
     * Indicates whether it's a re-scan.
     *
     * @var bool
     */
    private static $is_re_scan = false;

    public function __construct(
        Hide_Dashboard_Menu_Items_Debugger $debugger,
        Hide_Dashboard_Menu_Items_Notice_Manager $notice_manager
    ) {
        $this->debugger = $debugger;
        $this->notice_manager = $notice_manager;
    }

    /**
     * Handler for re-scan form submission.
     */
    public function re_scan_handler(): void
    {
        if ($this->verify_scan_nonce('_hdmi_re_scan_nonce_action', '_hdmi_re_scan_nonce_field')) {
            self::$is_re_scan = true;
            Hide_Dashboard_Menu_Items_Storage_Manager::add_scan_started_transient();

            $this->debugger->log_debug('Re-scan Initiated', current_time('Y-M-D H:i'));

            $this->notice_manager->add_plugin_notice(
                __('Re-scan completed successfully.', 'hide-dashboard-menu-items'),
                'success'
            );

            error_log(__METHOD__ . ' executed');
        }
    }

    /**
     * Handler for initial scan form submission.
     */
    public function init_scan_handler(): void
    {
        if ($this->verify_scan_nonce('hdmi_scan_nonce_action', 'hdmi_scan_nonce_field')) {
            self::$is_re_scan = false;
            Hide_Dashboard_Menu_Items_Storage_Manager::add_scan_started_transient();

            $this->debugger->log_debug('Initial scan started', current_time('Y-M-D H:i'));

            $this->notice_manager->add_plugin_notice(
                __('Initial scan has been completed successfully.', 'hide-dashboard-menu-items'),
                'success'
            );

            error_log(__METHOD__ . ' executed');
        }
    }

    /**
     * Scan dashboard (admin menu) items.
     */
    public function scan_dashboard(): void
    {
        if (!Hide_Dashboard_Menu_Items_Storage_Manager::has_scan_started()) return;

        global $menu, $submenu;

        if (empty($menu) || !is_array($menu)) {
            $this->debugger->log_error('Admin menu not initialized.');
            return;
        }

        $menu_items = [];

        foreach ($menu as $item) {
            $title = $this->extract_menu_title($item[0] ?? '');
            $capability = $item[1] ?? '';
            $slug = $item[2] ?? '';
            $dashicon = $item[6] ?? '';

            if (empty($slug) || empty($title) || empty($dashicon)) continue;
            if ($slug === Hide_Dashboard_Menu_Items_Config::settings_slug()) continue;
            if (!current_user_can($capability)) continue;

            $menu_items[] = compact('slug', 'title', 'dashicon');
        }

        if (empty($menu_items)) {
            $this->debugger->log_error('No top-level dashboard menu items found.');
            return;
        }

        Hide_Dashboard_Menu_Items_Storage_Manager::update_dashboard($menu_items);
        Hide_Dashboard_Menu_Items_Storage_Manager::update_dashboard_children($submenu);

        error_log(__METHOD__ . ' executed');
    }

    /**
     * Scan admin bar (toolbar) items.
     */
    public function scan_admin_bar($wp_admin_bar): void
    {
        if (!Hide_Dashboard_Menu_Items_Storage_Manager::has_scan_started()) return;

        if (!($wp_admin_bar instanceof WP_Admin_Bar)) {
            $this->debugger->log_error('Admin bar not available.');
            return;
        }

        $nodes = $wp_admin_bar->get_nodes();
        if (empty($nodes)) {
            $this->debugger->log_error('No admin bar nodes found.');
            return;
        }

        $top_level = [];
        $children = [];

        foreach ($nodes as $node) {
            $title = wp_strip_all_tags($node->title ?? '');

            if (empty($node->parent)) {
                if (empty($title) || $node->href === get_site_url() || $node->id === 'site-name') continue;

                $title = strpos($title, 'Comments') !== false ? 'Comments' : $title;

                $top_level[] = [
                    'id'    => $node->id,
                    'title' => $title,
                    'href'  => $node->href,
                ];
            } else {
                if (
                    empty($node->id) ||
                    empty($node->href) ||
                    str_starts_with($node->href, '#') ||
                    !str_contains($node->href, admin_url(''))
                ) continue;

                $children[$node->parent][] = [
                    'id'   => $node->id,
                    'href' => $node->href,
                ];
            }
        }

        if (empty($top_level)) {
            $this->debugger->log_error('No top-level admin bar items found.');
            return;
        }

        Hide_Dashboard_Menu_Items_Storage_Manager::update_admin_bar($top_level, $children);
        Hide_Dashboard_Menu_Items_Storage_Manager::update_admin_bar_children($children);

        update_option(Hide_Dashboard_Menu_Items_Config::scan_success_option(), 1);

        error_log(__METHOD__ . ' executed');
    }

    /**
     * Finalize scanning by removing transient.
     */
    public function finish_scan(): void
    {
        if (Hide_Dashboard_Menu_Items_Storage_Manager::has_scan_started()) {
            Hide_Dashboard_Menu_Items_Storage_Manager::remove_scan_started_transient();
            error_log(__METHOD__ . ' executed');
        }
    }

    /**
     * Verify nonce for scan requests.
     */
    private function verify_scan_nonce(string $action, string $field): bool
    {
        return isset($_POST[$field], $_POST['_wp_http_referer']) &&
            check_admin_referer($action, $field);
    }

    /**
     * Extract plain title from a menu item (strip HTML).
     */
    private function extract_menu_title(string $raw): string
    {
        if (strpos($raw, '<') !== false) {
            preg_match('/^[^<]+/', $raw, $matches);
            return isset($matches[0]) ? trim($matches[0]) : '';
        }
        return trim($raw);
    }
}
