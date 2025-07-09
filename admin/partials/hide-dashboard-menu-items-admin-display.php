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
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
// Check if the scan is already done
$scan_done = get_option('hdmi_scan_completed');

if (!$scan_done && !isset($_GET['hdmi_scan_success'])): ?>
    <style>
        #hdmi-scan-overlay {
            position: absolute;
            width: 100%;
            height: 100%;
            min-height: 100vh;
            inset: 0;
            background: rgba(255, 255, 255, 0.95);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            font-size: 18px;
        }

        #hdmi-scan-overlay button {
            margin-top: 1rem;
            padding: 0.6rem 1.2rem;
            font-size: 20px;
            background-color: #0073aa;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>

    <div id="hdmi-scan-overlay">
        <h1>Welcome</h1>
        <p class="hdmi">
            <strong>Before using this plugin, you need to scan the admin menu.</strong>
        </p>
        <form method="post" class="hdmi-first-scan-form">
            <input type="hidden" name="hdmi_scan_request" value="1">
            <?php submit_button('Start First Scan', 'primary', '', false, array('class' => 'hdmi-first-scan-submit')); ?>
        </form>
    </div>
<?php endif;

// If scan is done, show success message
if (isset($_GET['hdmi_scan_success'])) {
    add_settings_error(
        'hdmi_scan_notice',
        'hdmi_scan_success',
        'Admin menu items scanned and cached successfully!',
        'success'
    );
}

settings_errors('hdmi_scan_notice');

// Only show the rest of the form if scan is done
if (get_option('hdmi_scan_completed')): ?>

    <!-- <form method="post" action="options.php"></form> -->

<?php endif; ?>