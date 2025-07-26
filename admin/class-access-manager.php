<?php

/**
 * Access manager class for the plugin
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Hide_Dashboard_Menu_Items_Access_Manager
{

    /**
     * @var Hide_Dashboard_Menu_Items_Debugger
     */
    private $debugger;

    /**
     * Constructor
     */
    public function __construct(
        Hide_Dashboard_Menu_Items_Debugger $debugger,
    ) {
        $this->debugger = $debugger;
    }

    /**
     * Whether bypass is verified via nonce
     */
    protected function is_bypass_verified(): bool
    {
        return isset($_GET['_bypass_url_nonce']) && wp_verify_nonce(
            sanitize_text_field(wp_unslash($_GET['_bypass_url_nonce'])),
            'bypass_enabled_action'
        );
    }

    /**
     * Remove hidden menu items
     */
    public function remove_hidden_dashboard_items(): void
    {
        static $has_run = false;
        if ($has_run || Hide_Dashboard_Menu_Items_Storage_Manager::has_scan_started()) return;
        $has_run = true;

        global $menu;
        $hidden_items = Hide_Dashboard_Menu_Items_Storage_Manager::get_hidden_dashboard();

        if (!is_array($menu) || empty($hidden_items)) return;

        error_log(__METHOD__ . ' executing');

        foreach ($hidden_items as $slug) {
            remove_menu_page($slug);
        }
    }

    /**
     * Remove hidden admin bar items
     */
    public function remove_hidden_admin_bar_items(): void
    {
        static $has_run = false;
        if ($has_run || Hide_Dashboard_Menu_Items_Storage_Manager::has_scan_started()) return;
        $has_run = true;

        global $wp_admin_bar;
        $hidden_items = Hide_Dashboard_Menu_Items_Storage_Manager::get_hidden_admin_bar();

        if (!($wp_admin_bar instanceof WP_Admin_Bar) || empty($hidden_items)) return;

        error_log(__METHOD__ . ' executing');

        foreach ($hidden_items as $id) {
            $wp_admin_bar->remove_menu($id);
        }
    }

    /**
     * Check if the current screen matches a hidden menu slug
     */
    private function accessing_same_menu(string $slug, WP_Screen $screen): bool
    {
        $needles = array_filter([
            $screen->id ?? '',
            $screen->base ?? '',
            $screen->parent_base ?? '',
            $screen->parent_file ?? '',
        ]);

        foreach ($needles as $needle) {
            if (str_contains($slug, $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set a transient admin notice
     */
    private function set_bypass_notice(): void
    {
        set_transient('hdmi_admin_notice_transient', [
            'message'     => 'You are on bypass mode!',
            'type'        => 'warning',
            'dismissible' => false,
        ]);
    }

    /**
     * Handle bypass form submission
     */
    public function bypass_form_handler(): void
    {
        static $has_run = false;
        if ($has_run) return;
        $has_run = true;

        $key = Hide_Dashboard_Menu_Items_Config::bypass_passcode_key();

        if (
            !isset($_POST['_bypass_gateway_nonce'], $_POST[$key], $_POST['_wp_http_referer']) ||
            !check_admin_referer('_bypass_gateway_action', '_bypass_gateway_nonce')
        ) {
            return;
        }

        error_log(__METHOD__ . ' executing');

        $input_code    = sanitize_text_field(wp_unslash($_POST[$key]));
        $referer_url   = sanitize_url(wp_unslash($_POST['_wp_http_referer']));
        $stored_code   = Hide_Dashboard_Menu_Items_Storage_Manager::get_bypass_code();

        if ($input_code === $stored_code) {
            $this->debugger->log_debug('Bypass Granted?', 'Bypass access granted on' . current_time('Y-m-d H:i:s'));

            $url = wp_nonce_url($referer_url, 'bypass_enabled_action', '_bypass_url_nonce');

            $this->set_bypass_notice();
            wp_safe_redirect($url);
            exit;
        }
    }

    /**
     * Decide whether to show the gateway
     */
    private function maybe_render_gateway(bool $is_restricted): void
    {
        if (!$is_restricted) return;

        if (!$this->is_bypass_verified()) {
            status_header(403);
            $this->access_gateway_markup();
            exit;
        }

        $this->set_bypass_notice();
    }

    /**
     * Output access gateway markup
     */
    public function access_gateway_markup(): void
    {
        $bypass_active = Hide_Dashboard_Menu_Items_Storage_Manager::is_bypass_active();
        $bypass_key    = Hide_Dashboard_Menu_Items_Config::bypass_passcode_key();
        $form_action   = admin_url("admin-post.php");

        echo "<div class='wrap'>";
        echo "<h1>🔒 Access Restricted</h1>";

        if ($bypass_active) {
            echo '<p>This page is hidden. If you have setup a passcode, enter it below:</p>';
            echo "<form method='post' action='" . esc_attr($form_action) . "'>";
            wp_nonce_field('_bypass_gateway_action', '_bypass_gateway_nonce');
            echo "<input type='text' name='" . esc_attr($bypass_key) . "' class='regular-text' placeholder='Enter passcode' autocomplete='off' required />";
            submit_button('Unlock Access');
            echo "</form>";
        } else {
            echo "<p>This page is hidden and direct access is not allowed by admin.</p>";
        }

        echo "</div>";
    }

    /**
     * Restrict access to hidden items
     */
    public function menu_access_gateway(WP_Screen $screen): void
    {
        static $has_run = false;
        if ($has_run) return;
        $has_run = true;

        if (
            !is_admin() ||
            Hide_Dashboard_Menu_Items_Storage_Manager::has_scan_started() ||
            !Hide_Dashboard_Menu_Items_Storage_Manager::is_restrict_active()
        ) {
            return;
        }

        error_log(__METHOD__ . ' executing');

        $dashboard_items = Hide_Dashboard_Menu_Items_Storage_Manager::get_restricted_dashboard();
        $admin_bar_items = Hide_Dashboard_Menu_Items_Storage_Manager::get_restricted_admin_bar();

        foreach (array_merge($dashboard_items, $admin_bar_items) as $slug) {
            $restricted = $this->accessing_same_menu($slug, $screen);
            $this->maybe_render_gateway($restricted);
        }
    }
}
