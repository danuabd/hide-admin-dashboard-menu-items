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
class Hide_Dashboard_Menu_Items_Notice_Manager
{

    private static $notices = [];

    /**
     * Set a custom admin notice using transient.
     *
     * @since   1.0.0
     * @param   string  $message    Message to display.
     * @param   string  $type       Notice type: success, error, warning, info.
     * @param   int     $duration   Duration in seconds (default: 60s).
     */
    public function add_plugin_notice($message, $type = 'info', $dismissible = true)
    {
        $notice = array(
            'message' => $message,
            'type'    => $type,
            'dismissible' => $dismissible
        );

        self::$notices[] = $notice;
    }

    /**
     * Render notice using a transient.
     * 
     * @since 1.0.1
     */
    public function render_transient_notice()
    {
        $transient_notice = get_transient('hdmi_admin_notice_transient');

        if (!$transient_notice || empty($transient_notice)) return;

        wp_admin_notice(__($transient_notice['message'], 'hide-admin-dashboard-items'), array(
            'type' => $transient_notice['type'],
            'dismissible' => $transient_notice['dismissible']
        ));

        delete_transient('hdmi_admin_notice_transient');
    }


    /**
     * Display admin notices (one per notice).
     * 
     * @since   1.0.0
     */
    public function render_plugin_notices()
    {
        if (sizeof(self::$notices) < 1) return;

        foreach (self::$notices as $i => $notice) {

            wp_admin_notice(__($notice['message'], 'hide-admin-dashboard-items'), array(
                'type' => $notice['type'],
                'dismissible' => $notice['dismissible']
            ));
        }
    }
}
