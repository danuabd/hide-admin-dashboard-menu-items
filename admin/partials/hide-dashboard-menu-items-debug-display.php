<?php

/**
 * Debug page view for the plugin
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/partials
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="hdmi__debug">
    <h1 class="hdmi__debug-heading">Hide Dashboard Menu Items — Debug Info</h1>
    <p class="hdmi__debug-description">This page shows internal plugin data and environment details for debugging and support.</p>
    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section">
        <h2 class="hdmi__debug-subheading">Debug Info</h2>
        <p class="hdmi__debug-subtitle">Debugging info is as useful as the error log to troubleshoot issues during plugin execution.</p>

        <button data-type="copy" data-key="debugInfo" onclick="copyInfo(event)" id="hdmi__copy-debug" class="button-primary hdmi__copy-button">Copy Debug Info</button>

        <div class="hdmi__log-box hdmi__debug-box">
            <ul>
                <li><strong>Plugin version: </strong><?php echo esc_html($version); ?></li>

                <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Environment: </strong>
                    <ul class="hdmi__inside-list">
                        <li><strong>1. PHP Version: </strong><?php echo esc_html(PHP_VERSION ?? 'Not available'); ?></li>
                        <li><strong>2. WordPress Version: </strong><?php echo esc_html(get_bloginfo('version') ?? 'Not available'); ?></li>
                        <li><strong>3. Active Theme: </strong><?php echo esc_html(wp_get_theme()->get('Name') ?? 'Not available'); ?></li>
                        <li><strong>4. Active Plugins Count: </strong><?php echo esc_html(count(get_option('active_plugins') ?? [])); ?></li>
                        <li><strong>5. Memory Limit: </strong><?php echo esc_html(WP_MEMORY_LIMIT ?? 'Not available'); ?></li>
                    </ul>
                </li>

                <?php if (empty($current_user)): ?>
                    <li><strong>User Info: </strong>Info not available.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">User Info: </strong>
                        <ul class="hdmi__inside-list">
                            <li><strong>1. Name: </strong><?php echo esc_html($user_name ?? 'Not available'); ?></li>
                            <li><strong>2. Role/s: </strong><?php echo esc_html($user_roles ?? 'Not available'); ?></li>
                        </ul>
                    </li>
                <?php endif; ?>

                <li><strong>Scan done?: </strong><?php echo $scan_status ? 'Yes' : 'No'; ?></li>
                <li><strong>Dashboard Menu Count: </strong><?php echo esc_html(count($dashboard_cache ?? [])); ?></li>

                <?php if (empty($dashboard_cache)): ?>
                    <li><strong>Dashboard Menu: </strong>No dashboard menu items found.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Dashboard Menu: </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($dashboard_cache as $key => $value) {
                                if (!isset($value['title'])) continue;
                                $i_print = strval($i) . '. '; ?>
                                <li><strong><?php echo esc_html($i_print); ?></strong><?php echo esc_html($value['title']) ?></li>
                            <?php $i += 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li><strong>Admin bar Menu Count: </strong><?php echo esc_html(count($admin_bar_cache ?? [])); ?></li>

                <?php if (empty($admin_bar_cache)): ?>
                    <li><strong>Admin bar Menu: </strong>No admin bar menu items found.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Admin bar Menu: </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($admin_bar_cache as $key => $value) {
                                if (!isset($value['title'])) continue;
                                $i_print = strval($i) . '. ';
                            ?>
                                <li><strong><?php echo esc_html($i_print) ?></strong><?php echo esc_html($value['title']) ?></li>
                            <?php $i += 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (empty($hidden_dashboard)): ?>
                    <li><strong>Hidden Dashboard Menu: </strong>No menu items configured.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Hidden Dashboard Menu (parents): </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($hidden_dashboard as $key => $value) {
                                $i_print = strval($i) . '. ' ?>
                                <li><strong><?php echo esc_html($i_print) ?></strong><?php echo esc_html(str_replace('>', '', $value)) ?></li>
                            <?php $i += 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <?php if (empty($hidden_admin_bar)): ?>
                    <li><strong>Hidden Admin Bar Menu: </strong>No items configured.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Hidden Admin Bar Menu (parents): </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($hidden_admin_bar as $key => $value) {
                                $i_print =  strval($i) . '. '; ?>
                                <li><strong><?php echo esc_html($i_print)  ?></strong><?php echo esc_html(str_replace('>', '', $value)) ?></li>
                            <?php $i = 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>

                <li><strong>Direct access: </strong><?php echo $is_restrict_enabled ? 'Disabled' : 'Enabled'; ?></li>

                <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Bypass Settings: </strong>
                    <ul class="hdmi__inside-list">
                        <li><strong>1. Bypass Enabled: </strong><?php echo $bypass_enabled ? 'Yes' : 'No'; ?></li>
                        <li><strong>2. Bypass Passcode: </strong><?php echo $bypass_code ? 'is set' : 'is not set'; ?></li>
                    </ul>
                </li>

                <?php if (empty($debug_log)): ?>
                    <li><strong>Additional Info: </strong>No additional information available.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Additional Info: </strong>
                        <ul class="hdmi__inside-list">
                            <?php
                            $i = 1;
                            foreach ($debug_log as $key => $value) {
                                $i_print =  strval($i) . '. '; ?>
                                <li><strong><?php echo esc_html($i_print), esc_html($key) ?>: </strong><?php echo esc_html($value) ?></li>
                            <?php $i++;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section">
        <h2 class="hdmi__debug-subheading">Error Info</h2>
        <p class="hdmi__debug-subtitle">This shows the last 40 errors during plugin function executions.</p>
        <button data-key="errorInfo" onclick="copyInfo(event)" id="hdmi__copy-error" class="button-primary hdmi__copy-button">Copy Error Info</button>
        <div id="hdmi__error-box" class="hdmi__log-box">
            <ul>
                <?php
                $i = 1;
                foreach ($error_log as $key => $value) {
                    $i_print =  strval($i) . '. '; ?>
                    <li><strong><?php echo esc_html($i_print), esc_html($key);  ?>: </strong><?php echo esc_html($value) ?></li>
                <?php $i++;
                } ?>
            </ul>
        </div>
    </div>

    <hr class="hdmi__debug-divider">

    <div id="hdmi__debug-help" class="hdmi__debug-section">
        <strong>Need help?</strong> Contact <a href="mailto:support@danukaprasad.com">support@danukaprasad.com</a> or visit <a href="https://danukaprasad.com" target="_blank">danukaprasad.com</a>.
    </div>
</div>

<script>
    const debuggingData = {
        'debugInfo': <?php echo json_encode($debug_log ?? []); ?>,
        'errorInfo': <?php echo json_encode($error_log ?? []); ?>
    };

    const copyInfo = function(e) {
        if (!e.target.classList.contains('hdmi__copy-button')) return;

        const key = e.target.dataset.key;
        const data = debuggingData[key];
        const label = key === 'debugInfo' ? 'Debug Info' : 'Error Info';

        if (!data || Object.keys(data).length === 0) {
            alert(`No ${label} available to copy.`);
            return;
        }

        navigator.clipboard.writeText(JSON.stringify(data, null, 2))
            .then(() => alert(`${label} copied to clipboard!`))
            .catch(err => {
                console.error('Failed to copy:', err);
                alert(`Failed to copy ${label}.`);
            });
    };
</script>