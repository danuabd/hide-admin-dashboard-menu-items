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

if ($final_debug_info && $stored_error_data) return;
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
            <?php if (empty($stored_error_data)): ?>

                <li>No errors logged.</li>

            <?php else: ?>

                <?php

                foreach ($stored_error_data as $key => $value):

                    if ($key || $value) continue;

                ?>

                    <li>
                        <strong><?php echo esc_html($key) ?>: </strong>
                        <?php echo esc_html($value) ?>
                    </li>

                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    </div>

    <hr class="hdmi__debug-divider">

    <div class="hdmi__debug-section">
        <h2 class="hdmi__debug-subheading">Error Info</h2>
        <p class="hdmi__debug-subtitle">This part shows last 40 errors occurred during the plugin function executions.</p>
        <button data-key="errorInfo" onclick="copyInfo(event)" id="hdmi__copy-error" class="button-primary hdmi__copy-button">Copy Error Info</button>
        <div class="hdmi__log-box hdmi__error-box">
            <ul>
                <li>
                    <strong>Plugin version: </strong>
                    <?php echo isset($this->config->version) ? esc_html($this->config->version) : '1.0.0'  ?>
                </li>
                <li>
                    <strong>Environment: </strong>
                    <ul>
                        <li>
                            <strong>PHP Version: </strong>
                            <?php echo isset(PHP_VERSION) ? esc_html_e(PHP_VERSION) : 'Not available' ?>
                        </li>
                        <li>
                            <strong>WordPress Version: </strong>
                            <?php echo get_bloginfo('version') ? get_bloginfo('version') : 'Not available'  ?>
                        </li>
                        <li>
                            <strong>Active Theme: </strong>
                            <?php echo isset(wp_get_theme()->get('Name')) ? esc_html(wp_get_theme()->get('Name')) : 'Not available' ?>
                        </li>
                        <li>
                            <strong>Active Plugins Count: </strong>
                            <?php echo isset(get_option('active_plugins')) ? count(get_option('active_plugins')) : 'Not available' ?>
                        </li>
                        <li>
                            <strong>Memory Limit: </strong>
                            <?php echo isset(WP_MEMORY_LIMIT) ? esc_html(WP_MEMORY_LIMIT) : 'Not available' ?>
                        </li>
                    </ul>
                </li>
                <li>Current User:
                    <ul>
                        <li>
                            <strong>ID: </strong>
                            <?php echo isset($user->ID) ? esc_html($user->ID) : 'Not available' ?>
                        </li>
                        <li>
                            <strong>Username: </strong>
                            <?php echo isset($user->user_login) ? esc_html($user->user_login) : 'Not available' ?>
                        </li>
                        <li>
                            <strong>Roles: </strong>
                            <?php echo isset($user->roles) ? esc_html(implode(', ', $user->roles)) : 'Not available' ?>
                        </li>
                        <li>
                            <strong>Can manage_options: </strong>
                            <?php echo current_user_can('manage_options') ? 'Yes' : 'No' ?>
                        </li>
                    </ul>
                </li>
                <li>
                    <strong>Scan done?: </strong>
                    <?php echo $scan_status ? 'Yes' : 'No' ?>
                </li>
                <li>
                    <strong>Dashboard Menu Count: </strong>
                    <?php echo isset($db_menu_cache) ? count($db_menu_cache) : '0' ?>
                </li>
                <li>
                    <strong>Dashboard Menu: </strong>
                    <?php if (empty($db_menu_cache)): ?>

                        No dashboard menu items were found.

                    <?php else: ?>

                        <ul>

                            <?php

                            foreach ($db_menu_cache as $key => $value):

                                if ($key !== 'title') continue;

                            ?>
                                <li><?php echo esc_html($value) ?></li>

                            <?php endforeach; ?>

                        </ul>

                    <?php endif; ?>
                </li>
                <li>
                    <strong>Admin Bar Menu Count: </strong>
                    <?php echo $tb_menu_cache ? count($tb_menu_cache) : '0' ?>
                </li>
                <li>
                    <strong>Admin Bar Menu: </strong>
                    <?php if (empty($tb_menu_cache)): ?>

                        No admin bar menu items were found.

                    <?php else: ?>

                        <ul>

                            <?php

                            foreach ($tb_menu_cache as $key => $value):

                                if ($key !== 'title') continue;

                            ?>

                                <li><?php echo esc_html($value) ?></li>

                            <?php endforeach; ?>

                        </ul>

                    <?php endif; ?>
                </li>
                <li>
                    <strong>Hidden Dashboard Menu: </strong>
                    <?php if (empty($hidden_db_menus)): ?>

                        No hidden dashboard menu items configured.

                    <?php else: ?>

                        <ul>

                            <?php

                            foreach ($hidden_db_menus as $key => $value):

                                if ($key !== 'title') continue;

                            ?>

                                <li><?php echo esc_html($value) ?></li>

                            <?php endforeach; ?>

                        </ul>

                    <?php endif; ?>
                </li>
                <li>
                    <strong>Hidden Admin Bar Menu: </strong>
                    <?php if (empty($hidden_tb_menus)): ?>

                        No hidden admin bar menu items configured.

                    <?php else: ?>

                        <ul>

                            <?php

                            foreach ($hidden_tb_menus as $key => $value):

                                if ($key !== 'title') continue;

                            ?>

                                <li><?php echo esc_html($value) ?></li>

                            <?php endforeach; ?>

                        </ul>

                    <?php endif; ?>
                </li>
                <li>
                    <strong>Bypass Settings: </strong>
                    <ul>
                        <li>
                            Bypass Enabled: <?php echo $bypass_enabled ? 'Yes' : 'No' ?>
                        </li>
                        <li>
                            Bypass Query Key: <?php echo $bypass_key ? 'is set' : 'is not set' ?>
                        </li>
                    </ul>
                </li>
                <li>
                    <strong>Additional Info: </strong>
                    <?php if (empty($stored_info_data)):  ?>
                        No additional information is available at this time.
                    <?php else: ?>
                        <ul>
                            <?php

                            foreach ($stored_info_data as $key => $value):

                                if ($key || $value) continue;
                            ?>
                                <li>
                                    <?php echo esc_html($key) ?>: <?php echo esc_html($value) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </li>
            </ul>

        </div>
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