<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin
 * @author     ABD Prasad <contact@danukaprasasd.com>
 */
class Hide_Dashboard_Menu_Items_Admin
{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The name of this plugin option.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_option_name    The name of this plugin option.
	 */
	private $plugin_option_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The name of the option group for settings.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_option_group    The name of the option group for settings.
	 */
	private $plugin_option_group;


	/**
	 * The name of the option where settings are stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $settings_option    The name of the option where settings are stored.
	 */
	private $settings_option;

	/**
	 * The name of the option where menu items are cached.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $db_menu_option    The name of the option where menu items are cached.
	 */
	private $db_menu_option;

	/**
	 * The name of the option where admin toolbar menu items are cached.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $tb_menu_option    The name of the option where menu items are cached.
	 */
	private $tb_menu_option;

	/**
	 * The key of the option where hidden dashboard menus are stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hidden_db_menus_key    The key of the option where hidden menus are stored.
	 */
	private $hidden_db_menu_key;

	/**
	 * The key of the option where hidden admin toolbar menus are stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hidden_tb_menus_key    The key of the option where hidden menus are stored.
	 */
	private $hidden_tb_menu_key;

	/**
	 * The key of the option where bypass enabled status is stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $bypass_enabled_key	The key of the option where bypass enabled status is stored.
	 */
	private $bypass_enabled_key;

	/**
	 * The key of the option where bypass key is stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $bypass_key    The key of the option where bypass key is stored.
	 */
	private $bypass_param_key;

	/**
	 * The state of scan used to prevent conflicts between methods.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      boolean    $is_scanning    The state of scan.
	 */
	private $is_scanning = false;

	/**
	 * The name of the option where scan success status is stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $scan_success_option    The name of the option where scan success status is stored.
	 */
	private $scan_success_option;

	/**
	 * The slug for the settings page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $settings_page_slug    The slug for the settings page.
	 */
	private $settings_page_slug;

	/**
	 * The slug for the debug page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $debug_page_slug    The slug for the debug page.
	 */
	private $debug_page_slug;

	/**
	 * The name of the option where debug info is stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $debug_option    The name of the option where debug info is stored.
	 */
	private $debug_option;

	/**
	 * The hook suffix for the settings page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $settings_page_hook_suffix;

	/**
	 * The hook suffix for the debug page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $debug_page_hook_suffix;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct($plugin_name, $version)
	{

		$this->plugin_name = $plugin_name;
		$this->plugin_option_name = 'hdmi';
		$this->version = $version;

		// Define the option names for settings, cached menu items, scan success status, and hidden menus
		$this->plugin_option_group = $this->plugin_option_name . '_group';

		$this->settings_option = $this->plugin_option_name . '_settings';
		$this->db_menu_option = $this->plugin_option_name . '_db_cached';
		$this->tb_menu_option = $this->plugin_option_name . '_tb_cached';
		$this->scan_success_option = $this->plugin_option_name . '_scan_completed';
		$this->debug_option = $this->plugin_option_name . '_debug';

		$this->hidden_db_menu_key = 'hidden_db_menu';
		$this->hidden_tb_menu_key = 'hidden_tb_menu';
		$this->bypass_enabled_key = 'bypass_enabled';
		$this->bypass_param_key = 'bypass_param';

		// Define the slugs for the settings and debug pages
		$this->settings_page_slug = $this->plugin_name . '-settings';
		$this->debug_page_slug = $this->plugin_name . '-debug';
	}

	private function get_environment_info()
	{
		return [
			'Plugin Version' => $this->version,
			'Environment' => [
				'WordPress Version' => get_bloginfo('version'),
				'PHP Version' => PHP_VERSION,
				'Memory Limit' => WP_MEMORY_LIMIT,
				'Active Theme' => wp_get_theme()->get('Name'),
				'Active Plugins Count' => count($this->get_plugin_option('active_plugins', [])),
			]
		];
	}

	/**
	 * Get the plugin option value for a specific key.
	 *
	 * @since    1.0.0
	 * @param string $key The key to retrieve the option value for.
	 * @param mixed $default The default value to return if the key does not exist.
	 * @return mixed The option value or the default value if the key does not exist.
	 */
	private function get_plugin_option($key, $default = [])
	{
		$options = get_option($this->settings_option, []);
		return $options[$key] ?? $default;
	}

	/**
	 * Log a info message to the plugin's info log.
	 *
	 * @since 1.0.0
	 * @param string $key The key for the info message.
	 * @param string|array $message The info message to log.
	 */
	private function log_info($key, $message)
	{
		if (!$key || (!$message || empty($message))) {
			return;
		}
		$debug_data = get_option($this->debug_option, []);

		$debug_data['info'][$key] = $message;
		update_option($this->debug_option, $debug_data);
	}

	/**
	 * Log an error message to the plugin's error log.
	 *
	 * @since 1.0.0
	 * @param string $message The error message to log.
	 */
	private function log_error($message)
	{
		if (empty($message)) {
			return;
		}

		$debug_data = get_option($this->debug_option, []);

		$key = current_time('mysql');

		$debug_data['error'][$key] =  $message;

		// Keep only the last 50 entries
		if (count($debug_data['error']) > 50) {
			$debug_data['error'] = array_slice($debug_data['error'], -50, null, true);
		}

		update_option($this->debug_option, $debug_data);
		$this->log_timed_info('Last Error Log Updated');
	}

	/**
	 * Log an error message to the plugin's error log with time.
	 *
	 * @since 1.0.0
	 * @param string $key The error key to log.
	 */
	private function log_timed_info($key)
	{
		$this->log_info($key, current_time('mysql'));
	}


	/**
	 * Register the admin menu for this plugin (dashboard area).
	 *
	 * @since    1.0.0
	 */
	public function add_admin_menu()
	{
		// Add a new top-level menu item.
		$this->settings_page_hook_suffix =	add_menu_page(
			'Configure Hide Menu Items',
			__('Hide Menu Items', $this->plugin_name),
			'manage_options',
			$this->settings_page_slug,
			array($this, 'display_settings_page'),
			'dashicons-hidden',
			99
		);

		$this->debug_page_hook_suffix = add_submenu_page(
			$this->settings_page_slug,
			__('Debug Info', $this->plugin_name),
			__('Debug Info', $this->plugin_name),
			'manage_options',
			$this->debug_page_slug,
			[$this, 'display_debug_page']
		);
	}



	/**
	 * Register the settings fields and sections for this plugin.
	 *
	 * @since    1.0.0
	 */
	public function register_fields_and_sections()
	{
		add_settings_section(
			$this->plugin_name . '_settings_section',
			'',
			'__return_false',
			$this->settings_page_slug
		);
	}

	/**
	 * Set a custom admin notice using transient.
	 *
	 * @param string $key Unique key for this notice.
	 * @param string $message Message to display.
	 * @param string $type Notice type: success, error, warning, info.
	 * @param int $duration Duration in seconds (default: 30s).
	 */
	public function set_admin_notice($key, $message, $type = 'success', $duration = 30)
	{
		$notice = array(
			'message' => $message,
			'type'    => $type,
		);

		set_transient("hdmi_notice_{$key}", $notice, $duration);
	}

	/**
	 * Display admin notices (one per key).
	 */
	public function display_admin_notices()
	{
		$notice_keys = array('hdmi_scan_success', 'hdmi_settings_updated', 'hdmi_bypass_enabled');

		$is_dismissible = !in_array('hdmi_bypass_enabled', $notice_keys) ? 'is-dismissible' : '';

		foreach ($notice_keys as $key) {
			$transient_key = "hdmi_notice_{$key}";
			$notice = get_transient($transient_key);

			if ($notice && !empty($notice['message'])) {
				$type = esc_attr($notice['type'] ?? 'info');
				$message = esc_html($notice['message']);

				echo "<div class='notice notice-{$type} {$is_dismissible}'><p>{$message}</p></div>";

				delete_transient($transient_key);
			}
		}
	}


	/**
	 * Process scanning for menu items.
	 *
	 * @since    1.0.0
	 */
	public function scan_menus()
	{

		if (
			isset($_POST['hdmi_scan_request']) &&
			current_user_can('manage_options') &&
			check_admin_referer('hdmi_scan_nonce_action', 'hdmi_scan_nonce_field')
		) {

			$this->is_scanning = true;

			global $menu;

			if (!empty($menu) || is_array($menu)) $this->store_db_menu();

			global $wp_admin_bar;

			if (!empty($wp_admin_bar) || is_array($wp_admin_bar)) $this->store_tb_menu($wp_admin_bar);

			// add_action('admin_menu', [$this, 'store_db_menu'], 999, 1);
			// do_action('admin_menu');

			// add_action('admin_bar_menu', [$this, 'store_tb_menu'], 999, 1);
			// do_action('admin_bar_menu', $GLOBALS['wp_admin_bar']);

			// Store in DB
			update_option($this->scan_success_option, 1);
			$this->log_timed_info('Last Scan Time');

			// Redirect back with success transient
			$this->set_admin_notice('hdmi_settings_updated', __('Menu scan completed successfully.', 'hide-dashboard-menu-items'), 'success');
			wp_redirect(admin_url('admin.php?page=' . $this->settings_page_slug));
			exit;
		}
	}

	/**
	 * Get the registered top-level admin menu items.
	 *
	 * @since    1.0.0
	 */
	public function store_db_menu()
	{

		global $menu;

		if (empty($menu) || !is_array($menu)) {
			$this->log_error('Menu is not initialized or empty.');
			return;
		}

		$menu_items = array();
		$hidden_menu_items = $this->get_plugin_option($this->hidden_db_menu_key, []);

		$menu_combined = array_merge($menu, $hidden_menu_items);

		foreach ($menu_combined as $item) {
			$raw_title = isset($item[0]) ? $item[0] : '';

			// extract clean title
			if (strpos($raw_title, '<') !== false) {
				// Contains HTML, extract only the part before it
				preg_match('/^[^<]+/', $raw_title, $matches);
				$title = isset($matches[0]) ? trim($matches[0]) : '';
			} else {
				// No HTML, use as-is
				$title = trim($raw_title);
			}

			$capability = isset($item[1]) ? $item[1] : '';
			$slug       = isset($item[2]) ? $item[2] : '';
			$icon       = isset($item[6]) ? $item[6] : '';

			if (empty($slug) || empty($title) || empty($icon) || $slug === $this->settings_page_slug) {
				// Skip items with missing data
				continue;
			}

			if (current_user_can($capability)) {
				$menu_items[] = array(
					'slug'     => $slug,
					'title'    => $title,
					'dashicon' => $icon,
				);
			}
		}

		if (empty($menu_items)) {
			$this->log_error('No top-level menu items found.');
			return;
		}

		error_log(print_r($menu_items));

		update_option($this->db_menu_option, $menu_items);
	}

	/**
	 * Get the registered top-level toolbar menu items.
	 *
	 * @since    1.0.0
	 */
	public function store_tb_menu($wp_admin_bar)
	{

		if (!is_object($wp_admin_bar)) {
			$this->log_error('WP Admin Bar is not initialized.');
			return;
		}

		$nodes = $wp_admin_bar->get_nodes();

		if (empty($nodes)) {
			$this->log_error('No admin bar nodes found.');
			return;
		}

		$menu_items  = array();

		foreach ($nodes as $node) {
			if (empty($node->parent)) {

				$title = strip_tags($node->title);
				if (empty($title) || $node->href === get_site_url() || $node->id === 'site-name') {
					continue;
				}

				$title = strpos($title, 'Comments') !== false ? 'Comments' : $title;

				$menu_items[] = array(
					'id'    => $node->id,
					'title' => $title,
				);
			}
		}

		if (empty($menu_items)) {
			$this->log_error('No top-level admin bar menu items found.');
			return;
		}

		update_option($this->tb_menu_option, $menu_items);
	}

	/**
	 * Generate the debug array in a structured format.
	 *
	 * @since    1.0.0
	 * @param array $array The array to generate.
	 * @param int $depth The current depth of recursion.
	 * @return string The generated HTML for the array.
	 */
	private function generate_debug_markup($array, $depth = 0)
	{
		if (!is_array($array)) {
			return esc_html((string)$array);
		}

		$output = "<ul style='margin-left: " . (20 * $depth) . "px; list-style-type: none;'>";

		foreach ($array as $key => $value) {
			$key = esc_html((string)$key);
			if (is_array($value)) {
				$output .= "<li><strong>{$key}:</strong> " . $this->generate_debug_markup($value, $depth + 1) . "</li>";
			} else {
				$value = esc_html((string)$value);
				$output .= "<li><strong>{$key}:</strong> {$value}</li>";
			}
		}

		$output .= "</ul>";

		return $output;
	}


	/**
	 * Display the debug page.
	 *
	 * @since    1.0.0
	 */
	public function display_debug_page()
	{
		if (!current_user_can('manage_options')) {
			return;
		}

		$scan_status = $this->get_plugin_option($this->scan_success_option, false);

		if (!$scan_status) {
			$this->log_error('Scan has not been completed. Please run the scan first.');
		}

		$db_menu_cache = get_option($this->db_menu_option, array());
		$tb_menu_cache = get_option($this->tb_menu_option, array());

		$hidden_db_menus = $this->get_plugin_option($this->hidden_db_menu_key, 'No hidden dashboard menu items configured.');
		$hidden_tb_menus = $this->get_plugin_option($this->hidden_tb_menu_key, 'No hidden admin bar menu items configured.');

		$stored_debug_data = $this->get_plugin_option($this->debug_option, []);
		$stored_info_data = $stored_debug_data['info'] ?? [];
		$stored_error_data = $stored_debug_data['error'] ?? [];

		$user = wp_get_current_user();

		$bypass_enabled = $this->get_plugin_option($this->bypass_enabled_key, false);
		$bypass_key = $this->get_plugin_option($this->bypass_param_key, false);

		if ($bypass_enabled && empty($bypass_key)) {
			$this->log_error('Bypass is enabled but no bypass key is set. Please configure the bypass key in the plugin settings.');
		}

		$curr_info_data = $this->get_environment_info();

		$curr_info_data['Database Menu Count'] = count($db_menu_cache);
		$curr_info_data['Database Menu'] = $db_menu_cache;
		$curr_info_data['Admin Bar Menu Count'] = count($tb_menu_cache);
		$curr_info_data['Admin Bar Menu'] = $tb_menu_cache;

		$curr_info_data['Current User'] = [
			'ID' => $user->ID,
			'Username' => $user->user_login,
			'Roles' => implode(', ', $user->roles),
			'Can manage_options' => current_user_can('manage_options') ? 'Yes' : 'No',
		];

		$curr_info_data['Hidden Dashboard Menu'] = $hidden_db_menus;
		$curr_info_data['Hidden Admin Bar Menu']	 = $hidden_tb_menus;
		$curr_info_data['Bypass Settings']	 = [
			'Bypass Enabled' => $bypass_enabled ? 'Yes' : 'No',
			'Bypass Query Key' => $bypass_key ? 'is set' : 'is not set',
		];

		$final_info_data = array_merge($curr_info_data, $stored_info_data);

		$debug_markup = $this->generate_debug_markup($final_info_data);
		$error_markup = empty($stored_error_data) ? '<li>No errors logged.</li>' : $this->generate_debug_markup($stored_error_data);

		require_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-debug-display.php';
	}


	/**
	 * Function to hide Dashboard Menu items.
	 *
	 * @since    1.0.0
	 */
	public function hide_db_menu()
	{
		if ($this->is_scanning) return;

		global $menu;

		$db_hidden = $this->get_plugin_option($this->hidden_db_menu_key, array());

		$bypass_active = $this->is_bypass_active();
		$bypass_param = $this->get_bypass_param();
		$bypass_param_key = $this->plugin_option_name;

		$bypass_param_in_uri = isset($_GET[$bypass_param_key]) && sanitize_text_field($_GET[$bypass_param_key])  === $bypass_param;

		if (!is_array($db_hidden) || empty($db_hidden)) {
			return;
		}

		if ($bypass_active && $bypass_param_in_uri) {
			$this->log_info('Bypass Active', 'Yes');
			$this->set_admin_notice('hdmi_bypass_enabled', __('Bypass is active and has been accessed', 'hide-dashboard-menu-items'), 'info');
			$this->update_db_menu($db_hidden, $bypass_param);
			return;
		}

		// No access — remove the menu items
		foreach ($db_hidden as $slug) {
			remove_menu_page($slug);
		}
	}

	/**
	 * Function to hide Admin Toolbar items.
	 *
	 * @since 1.0.0
	 */
	public function hide_tb_menu()
	{
		if ($this->is_scanning) return;

		global $wp_admin_bar;

		$tb_hidden = $this->get_plugin_option($this->hidden_tb_menu_key, array());

		$bypass_active = $this->is_bypass_active();
		$bypass_param = $this->get_bypass_param();
		$bypass_param_key = $this->plugin_option_name;

		$bypass_param_in_uri = isset($_GET[$bypass_param_key]) && sanitize_text_field($_GET[$bypass_param_key])  === $bypass_param;

		if (!is_array($tb_hidden) || empty($tb_hidden)) {
			return;
		}

		if ($bypass_active && $bypass_param_in_uri) {
			$this->log_info('Bypass Active', 'Yes');
			$this->set_admin_notice('hdmi_bypass_enabled', __('Bypass is active and has been accessed', 'hide-dashboard-menu-items'), 'info');
			$this->update_tb_menu($tb_hidden, $bypass_param);
			return;
		}

		foreach ($tb_hidden as $id) {
			$wp_admin_bar->remove_menu($id);
		}
	}

	/**
	 * Append the bypass query parameter to dashboard menu item URLs.
	 *
	 * @since    1.0.0
	 * @param array $menu The global menu array.
	 * @param array $hidden The hidden menu slugs.
	 * @param string $bypass_key The bypass query key.
	 */
	public function update_db_menu($hidden, $bypass_key)
	{
		global $menu;

		foreach ($menu as $index => $menu_item) {
			if (in_array($menu_item[2], $hidden, true)) {
				if (strpos($menu[$index][2], $bypass_key) === false) {
					if (strpos($menu[$index][2], '?') !== false) {
						$menu[$index][2] .= '&' . $this->plugin_option_name . '=' . $bypass_key;
					} else {
						$menu[$index][2] .= '?' . $this->plugin_option_name . '=' . $bypass_key;
					}
				}
			}
		}

		$this->log_info('Dashboard menu updated?', 'Yes');
		$this->log_timed_info('Dashboard menu was updated at');
	}


	/**
	 * Append the bypass query parameter to admin toolbar menu item URLs.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 * @param array        $hidden_slugs
	 * @param string       $bypass_key
	 */
	public function update_tb_menu($hidden_slugs, $bypass_key)
	{
		global $wp_admin_bar;

		foreach ($hidden_slugs as $slug) {
			$node = $wp_admin_bar->get_node($slug);

			if ($node && isset($node->href)) {
				if (strpos($node->href, $bypass_key) === false) {
					$updated_href = add_query_arg($this->plugin_option_name, $bypass_key, $node->href);
					$node->href = $updated_href;
					$wp_admin_bar->add_menu($node);
				}
			}
		}

		$this->log_info('Admin bar menu updated?', 'Yes');
		$this->log_timed_info('Admin bar menu was updated at');
	}


	/**
	 * Get the bypass query parameter if enabled.
	 *
	 * @since    1.0.0
	 * @return   string Returns the bypass query parameter if enabled, otherwise empty string.
	 */
	private function get_bypass_param()
	{
		if ($this->is_bypass_active())
			return $this->get_plugin_option($this->bypass_param_key, '');
		else return '';
	}

	/**
	 * Get the bypass active status.
	 *
	 * @since    1.0.0
	 * @return   bool Returns true if query parameter if enabled, otherwise false.
	 */
	private function is_bypass_active()
	{
		return $this->get_plugin_option($this->bypass_enabled_key, false);
	}

	/**
	 * Function to restrict access to hidden menu items.
	 *
	 * @since    1.0.0
	 */
	public function restrict_hidden_menu_access()
	{

		$hidden_db_menu = $this->get_plugin_option($this->hidden_db_menu_key, array());
		$hidden_tb_menu = $this->get_plugin_option($this->hidden_tb_menu_key, array());

		if (empty($hidden_db_menu) && empty($hidden_tb_menu)) {
			return;
		}

		$bypass_active = $this->is_bypass_active();
		$bypass_param = $this->get_bypass_param();
		$bypass_param_key = $this->plugin_option_name;

		$has_access = $bypass_active && isset($_GET[$bypass_param_key]) && sanitize_text_field($_GET[$bypass_param_key]) === $bypass_param;

		$hidden_all = array_merge($hidden_db_menu, $hidden_tb_menu);

		$current_screen = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : basename($_SERVER['PHP_SELF']);

		foreach ($hidden_all as $slug) {
			if (
				strpos($_SERVER['REQUEST_URI'], $slug) !== false
				|| $current_screen === $slug
			) {
				// allow access
				if ($has_access) {
					return;
				}

				// Otherwise, restrict access
				status_header(403);
				nocache_headers();
				exit;
			}
		}
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles($hook_suffix)
	{
		$css_base_url = plugin_dir_url(__FILE__) . 'css/hide-dashboard-menu-items-';
		$css_base_path = plugin_dir_path(__FILE__) . 'css/hide-dashboard-menu-items-';

		// load styles only in plugin admin settings page
		if ($hook_suffix === $this->settings_page_hook_suffix || $hook_suffix === $this->debug_page_hook_suffix) {
			wp_enqueue_style($this->settings_page_slug, $css_base_url . 'admin.css', array(), filemtime($css_base_path . 'admin.css'), 'all');
		}

		if ($hook_suffix === $this->debug_page_hook_suffix) {
			wp_enqueue_style($this->debug_page_slug, $css_base_url . 'debug.css', array(), filemtime($css_base_path . 'debug.css'), 'all');
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts($hook_suffix)
	{
		// load script only in plugin admin settings page
		if ($hook_suffix !== $this->settings_page_hook_suffix) {
			return;
		}

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hide-dashboard-menu-items-admin.js', array(), plugin_dir_path(__FILE__) . 'js/hide-dashboard-menu-items-admin.js', false);
	}
}
