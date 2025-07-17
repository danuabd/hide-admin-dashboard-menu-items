<?php

/**
 * Admin notice class for the plugin
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
class Hide_Dashboard_Menu_Items_Notices
{

    /**
     * Set a custom admin notice using transient.
     *
     * @param string $key Unique key for this notice.
     * @param string $message Message to display.
     * @param string $type Notice type: success, error, warning, info.
     * @param int $duration Duration in seconds (default: 30s).
     */
    public function add_notice($key, $message, $type = 'info', $duration = 30)
    {
        $notice = array(
            'message' => $message,
            'type'    => $type,
        );

        set_transient("hdmi_notice_{$key}", $notice, $duration);
    }


    /**
     * Display admin notices (one per key).
     */
    public function render_notices()
    {
        $notice_keys = array('scan_completed', 'settings_updated', 'bypass_enabled');

        foreach ($notice_keys as $key) {
            $transient_key = "hdmi_notice_{$key}";
            $notice = get_transient($transient_key);

            $dismissible_attr = $key !== 'bypass_enabled' ? 'is-dismissible' : '';

            if ($notice && !empty($notice['message'])) {
                $type = esc_attr($notice['type'] ?? 'info');
                $message = esc_html($notice['message']);

                echo "<div class='notice notice-{$type} {$dismissible_attr}'><p>{$message}</p></div>";

                delete_transient($transient_key);
            }
        }
    }
}
