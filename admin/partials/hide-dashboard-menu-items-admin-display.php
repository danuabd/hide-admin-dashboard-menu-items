<?php

/**
 * Admin area view for the plugin
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/partials
 */
if (isset($_GET['hdmi_scan_success'])) {
    add_settings_error(
        'hdmi_scan_notice',
        'hdmi_scan_success',
        'Initial admin menu scan completed successfully!',
        'success'
    );
}

settings_errors('hdmi_scan_notice');
