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
$icon_url = plugins_url('assets/icon-128x128.png', dirname(__FILE__, 2));
$background_img_path = plugins_url('assets/overlay-2560x1440.svg', dirname(__FILE__, 2));

?>
<div id="hdmi-scan">
    <img id="hdmi-scan__background" src="<?php echo esc_url($background_img_path) ?>" alt="Hide dashboard menu items background">
    <figure id="hdmi-scan__figure">
        <img id="hdmi-scan__logo" src="<?php echo esc_url($icon_url) ?>" alt="Hide dashboard menu items logo">
    </figure>
    <h1 id="hdmi-scan__title"><?php echo esc_html($title) ?></h1>
    <p id="hdmi-scan__description">
        <strong><?php echo esc_html($description) ?></strong>
    </p>
    <form id="hdmi-scan__form" method="post">
        <?php wp_nonce_field('hdmi_scan_nonce_action', 'hdmi_scan_nonce_field'); ?>
        <?php submit_button('Start First Scan', 'primary', '', false, array('id' => 'hdmi-scan__button', 'class' => 'hdmi__button')); ?>
    </form>
</div>