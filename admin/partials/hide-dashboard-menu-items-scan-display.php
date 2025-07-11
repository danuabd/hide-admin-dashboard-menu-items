<?php

/**
 * Scan view for the plugin
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
?>
<div id="hdmi-scan-overlay">
    <h1 id="hdmi-scan-title"><?php echo esc_html($title) ?></h1>
    <p id="hdmi-scan-description">
        <strong><?php echo esc_html($description) ?></strong>
    </p>
    <form method="post">
        <input type="hidden" name="hdmi_scan_request" value="1">
        <?php submit_button('Start First Scan', 'primary', '', false, array('id' => 'hdmi-scan-request-button')); ?>
    </form>
</div>