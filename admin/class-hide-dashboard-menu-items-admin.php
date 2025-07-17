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

	private function load_dependencies()
	{
		require_once plugin_dir_path(__FILE__) . 'class-admin-settings.php';
		require_once plugin_dir_path(__FILE__) . 'class-admin-debugger.php';
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
