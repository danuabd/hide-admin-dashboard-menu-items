<?php

/**
 * Debug page view for the plugin
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

<div id="hdmi__debug">
    <h1 class="hdmi__debug-heading">Hide Dashboard Menu Items — Debug Info</h1>
    <p class="hdmi__debug-description">This page shows internal plugin data and environment details for debugging and support.</p>

    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section">
        <h2 class="hdmi__debug-subheading">Debug Info</h2>
        <p class="hdmi__debug-subtitle">Debugging info is as useful as error log to troubleshoot any issues occur during plugin functionality executions.</p>
        <button data-type="copy" data-key="debugInfo" onclick="copyInfo(event)" id="hdmi__copy-debug" class="button-primary hdmi__copy-button">Copy Debug Info</button>
        <div class="hdmi__log-box hdmi__debug-box"><?php echo esc_html($debug_markup) ?></div>
    </div>

    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section">
        <h2 class="hdmi__debug-subheading">Error Info</h2>
        <p class="hdmi__debug-subtitle">This part shows last 40 errors occurred during the plugin function executions.</p>
        <button data-key="errorInfo" onclick="copyInfo(event)" id="hdmi__copy-error" class="button-primary hdmi__copy-button">Copy Error Info</button>
        <div class="hdmi__log-box hdmi__error-box"><?php echo esc_html($error_markup) ?></div>
    </div>

    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section hdmi__debug-help">
        <strong>Need help?</strong> Contact <a href="mailto:support@danukaprasad.com">support@danukaprasad.com</a> or visit <a href="https://danukaprasad.com" target="_blank">danukaprasad.com</a>.
    </div>
</div>

<script>
    const debuggingData = {
        'debugInfo': <?php echo json_encode($final_debug_info ?? []) ?>,
        'errorInfo': <?php echo json_encode($stored_error_data ?? []) ?>
    }

    const copyInfo = function(e) {
        if (!e.target.classList.contains('hdmi__copy-button')) return;

        const key = e.target.dataset.key;
        const data = debuggingData[key];
        const dataType = `${key.charAt(0).toUpperCase() + key.slice(1, key.indexOf('I')) + ' Info'
        }`;

        if (!data || data.length === 0) {
            alert(`No ${dataType} available to copy.`);
            return;
        }

        navigator.clipboard.writeText(data).then(() => {
            alert(`${dataType} copied to clipboard!`);
        }).catch(err => {
            console.error('Failed to copy:', err);
            alert(`Failed to copy ${dataType}. Please try again.`);
        });
    };
</script>