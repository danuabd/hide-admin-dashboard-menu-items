<?php

/**
 * Settings class for the plugin
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
     * Register settings for this plugin
     * 
     * @since 1.0.0
     */
    public function register_settings()
    {

        register_setting(
            $this->plugin_option_group,
            $this->settings_option,
            array($this, 'sanitize_submissions')
        );
    }

    /**
     * Register the settings page for this plugin.
     * 
     * @since    1.0.0
     */
    public function render_settings_page()
    {
        // Check if the user has the required capability.
        if (!current_user_can('manage_options')) {
            return;
        }

        // Include the settings page template.
        include_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-admin-display.php';
    }

    /**
     * Sanitize user inputs
     * 
     * @since 1.0.0
     * 
     * @param array $input User inputs received via admin form
     * @return array Sanitized array of options
     */
    public function sanitize_settings($input)
    {
        $this->log_timed_info('Settings form last submitted');

        if (!is_array($input)) {
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
            // You can choose to filter out other types (objects, etc.) or keep them
            return '';
        };

        $sanitized = array_map($sanitize_recursive, $input);

        if (!empty($sanitized)) {
            $this->log_timed_info('Settings last updated');
        }

        $this->set_admin_notice('hdmi_settings_updated', __('Settings have been updated.', 'hide-dashboard-menu-items'), 'success');
        return $sanitized;
    }
}
