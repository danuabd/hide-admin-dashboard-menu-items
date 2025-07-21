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
        <div class="hdmi__log-box hdmi__debug-box">
            <ul>
                <li><strong>Plugin version: </strong><?php echo esc_html($version) ?>
                </li>
                <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Environment: </strong>
                    <ul class="hdmi__inside-list">
                        <li><strong>1. PHP Version: </strong><?php echo PHP_VERSION !== null ? esc_html(PHP_VERSION) : 'Not available' ?>
                        </li>
                        <li><strong>2. WordPress Version: </strong><?php echo get_bloginfo('version') !== null ? esc_html(get_bloginfo('version')) : 'Not available'  ?>
                        </li>
                        <li><strong>3. Active Theme: </strong><?php echo wp_get_theme()->get('Name') !== null ? esc_html(wp_get_theme()->get('Name')) : 'Not available' ?>
                        </li>
                        <li><strong>4. Active Plugins Count: </strong><?php echo get_option('active_plugins') !== null ? count(get_option('active_plugins')) : 'Not available' ?>
                        </li>
                        <li><strong>5. Memory Limit: </strong><?php echo WP_MEMORY_LIMIT !== null ? esc_html(WP_MEMORY_LIMIT) : 'Not available' ?>
                        </li>
                    </ul>
                </li>
                <?php if (empty($user)): ?>
                    <li>User Info: </strong>Info not available.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">User Info: </strong>
                        <ul class="hdmi__inside-list">
                            <li><strong>1. ID: </strong><?php echo $current_user_id ? esc_html($current_user_id) : 'Not available' ?></li>
                            <li><strong>2. Name: </strong><?php echo $user_name ? esc_html($user_name) : 'Not available' ?></li>
                            <li><strong>3. Role/s: </strong><?php echo !empty($user_roles) ? esc_html($user_roles) : 'Not available' ?></li>
                        </ul>
                    </li>
                <?php endif; ?>
                <li><strong>Scan done?: </strong><?php echo $scan_status ? 'Yes' : 'No' ?></li>
                <li><strong>Dashboard Menu Count: </strong><?php echo !empty($dashboard_menu_cache) ? count($dashboard_menu_cache) : '0' ?></li>
                <?php if (empty($dashboard_menu_cache)): ?>
                    <li><strong>Dashboard Menu: </strong>No dashboard menu items were found.</li>
                <?php else : ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Dashboard Menu: </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($dashboard_menu_cache as $key => $value) {
                                if (!isset($value['title'])) continue;
                                $i_print = strval($i) . '. '; ?>
                                <li><strong><?php echo esc_html($i_print); ?></strong><?php echo esc_html($value['title']) ?></li>
                            <?php $i += 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <li><strong>Admin bar Menu Count: </strong><?php echo !empty($admin_bar_menu_cache) ? count($admin_bar_menu_cache) : '0' ?></li>
                <?php if (empty($admin_bar_menu_cache)): ?>
                    <li><strong>Admin bar Menu: </strong>No Admin bar menu items were found.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Admin bar Menu: </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($admin_bar_menu_cache as $key => $value) {
                                if (!isset($value['title'])) continue;
                                $i_print = strval($i) . '. ';
                            ?>
                                <li><strong><?php echo esc_html($i_print) ?></strong><?php echo esc_html($value['title']) ?></li>
                            <?php $i += 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (empty($hidden_dashboard_menu)): ?>
                    <li><strong>Hidden Dashboard Menu: </strong>No menu items configured.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Hidden Dashboard Menu: </strong>
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($hidden_dashboard_menu as $key => $value) {
                                $i_print = strval($i) . '. ' ?>
                                <li><strong><?php echo esc_html($i_print) ?></strong><?php echo esc_html(str_replace('>', '', $value)) ?></li>
                            <?php $i += 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <?php if (empty($hidden_admin_bar_menu)): ?>
                    <li><strong>Hidden Admin Bar Menu: </strong>No hidden admin bar menu items configured.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside">
                        <ul class="hdmi__inside-list">
                            <?php $i = 1;
                            foreach ($hidden_admin_bar_menu as $key => $value) {
                                $i_print =  strval($i) . '. '; ?>
                                <li><strong><?php echo esc_html($i_print)  ?></strong><?php echo esc_html(str_repeat('>', '', $value)) ?></li>
                            <?php $i = 1;
                            } ?>
                        </ul>
                    </li>
                <?php endif; ?>
                <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Bypass Settings: </strong>
                    <ul class="hdmi__inside-list">
                        <li><strong>1. Bypass Enabled: </strong><?php echo $bypass_enabled ? 'Yes' : 'No' ?></li>
                        <li><strong>2. Bypass Query Key: </strong><?php echo $bypass_key ? 'is set' : 'is not set' ?></li>
                    </ul>
                </li>
                <?php if (empty($debug_log)): ?>
                    <li><strong>Additional Info: </strong>No additional information is available at this time.</li>
                <?php else: ?>
                    <li class="hdmi__has-list-inside"><strong class="hdmi__list-after">Additional Info: </strong>
                        <ul class="hdmi__inside-list">
                            <?php
                            $i = 1;
                            foreach ($debug_log as $key => $value) {
                                $i_print =  strval($i) . '. '; ?>
                                <li><strong><?php echo esc_html($i_print), esc_html($key) ?>: </strong><?php echo esc_html($value) ?></li>
                            <?php } ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section">
        <h2 class="hdmi__debug-subheading">Error Info</h2>
        <p class="hdmi__debug-subtitle">This part shows last 40 errors occurred during the plugin function executions.</p>
        <button data-key="errorInfo" onclick="copyInfo(event)" id="hdmi__copy-error" class="button-primary hdmi__copy-button">Copy Error Info</button>
        <div id="hdmi__error-box" class="hdmi__log-box">
            <?php if (empty($error_log)): ?>
                <ul>
                    <li>No errors logged.</li>
                </ul>
            <?php else: ?>
                <ul>
                    <?php $i = 1;
                    foreach ($error_log as $key => $cvalue) {
                        $i_print =  strval($i) . '. '; ?>
                        <li><strong><?php echo esc_html($i_print), esc_html($key);  ?>: </strong><?php echo esc_html($value); ?></li>
                    <?php $i += 1;
                    } ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>

    <hr class="hdmi__debug-divider">

    <div id="hdmi__debug-help" class="hdmi__debug-section">
        <strong>Need help?</strong> Contact <a href="mailto:support@danukaprasad.com">support@danukaprasad.com</a> or visit <a href="https://danukaprasad.com" target="_blank">danukaprasad.com</a>.
    </div>
</div>

<script>
    const debuggingData = {
        'debugInfo': <?php echo json_encode($debug_log ?? []) ?>,
        'errorInfo': <?php echo json_encode($error_log ?? []) ?>
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