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
<div id="hdmi-scan">
    <?php include_once plugin_dir_path(__FILE__) . '../../assets/overlay-2560x1440.svg' ?>
    <figure id="hdmi-scan__figure">
        <img class="hdmi-scan__logo" src="<?php echo plugin_dir_url(__FILE__) . '../../assets/icon-128x128.png' ?>" alt="" srcset="">
    </figure>
    <h1 id="hdmi-scan__title"><?php echo esc_html($title) ?></h1>
    <p id="hdmi-scan__description">
        <strong><?php echo esc_html($description) ?></strong>
    </p>
    <form id="hdmi-scan__form" method="post">
        <input id="hdmi-scan__input" type="hidden" name="hdmi_scan_request" value="1">
        <?php submit_button('Start First Scan', 'primary', '', false, array('id' => 'hdmi-scan__button')); ?>
    </form>
</div>