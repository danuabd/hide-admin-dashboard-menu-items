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
	 * @var      string    $db_menu_items_option    The name of the option where menu items are cached.
	 */
	private $db_menu_items_option;

	/**
	 * The name of the option where admin toolbar menu items are cached.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $tb_menu_items_option    The name of the option where menu items are cached.
	 */
	private $tb_menu_items_option;

	/**
	 * The key of the option where hidden dashboard menus are stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hidden_db_menus_key    The key of the option where hidden menus are stored.
	 */
	private $hidden_db_menus_key;

	/**
	 * The key of the option where hidden admin toolbar menus are stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hidden_db_menus_key    The key of the option where hidden menus are stored.
	 */
	private $hidden_tb_menus_key;

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
	private $bypass_query_key;

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
		$this->version = $version;

		// Define the option names for settings, cached menu items, scan success status, and hidden menus
		$this->plugin_option_group = $this->plugin_name . '_group';

		$this->settings_option = $this->plugin_name . '_settings';
		$this->db_menu_items_option = $this->plugin_name . '_db_cached';
		$this->tb_menu_items_option = $this->plugin_name . '_tb_cached';
		$this->scan_success_option = '_scan_completed';

		$this->hidden_db_menus_key = 'hidden_db_menus';
		$this->hidden_tb_menus_key = 'hidden_tb_menus';
		$this->bypass_enabled_key = 'bypass_enabled';
		$this->bypass_query_key = 'bypass_key';

		// Define the slugs for the settings and debug pages
		$this->settings_page_slug = $this->plugin_name . '-settings';
		$this->debug_page_slug = $this->plugin_name . '-debug';
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
			'Hide Menu Items',
			'manage_options',
			$this->settings_page_slug,
			array($this, 'display_settings_page'),
			'dashicons-hidden',
			99
		);
	}

	/**
	 * Register settings for this plugin
	 * 
	 * @since 1.0.0
	 */
	public function register_settings()
	{
		register_setting(
			$this->plugin_option_group,
			$this->settings_option,
			array($this, 'sanitize_settings_options')
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
	 * Process scanning for menu items.
	 *
	 * @since    1.0.0
	 */
	public function process_triggered_scan()
	{
		if (
			isset($_POST['hdmi_scan_request']) &&
			current_user_can('manage_options')
		) {

			// Temporarily hook into admin_bar_menu
			add_action('admin_bar_menu', [$this, 'get_tb_menu_items'], 999, 1);

			// Trigger admin bar rendering to collect items
			do_action('admin_bar_menu', $GLOBALS['wp_admin_bar']);

			// Store in DB
			// ← Add this line
			update_option($this->scan_success_option, 1);

			// Redirect back with success transient
			set_transient('hdmi_scan_success_notice', true, 30);
			wp_redirect(admin_url('admin.php?page=' . $this->settings_page_slug));
			exit;
		}
	}

	/**
	 * Get the registered top-level admin menu items.
	 *
	 * @since    1.0.0
	 * @return   array    An array of top-level admin menu items.
	 */
	public function get_db_menu_items()
	{
		global $menu;

		$menu_items = array();

		foreach ($menu as $item) {
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

			if (empty($slug) || empty($title) || empty($icon)) {
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

		update_option($this->db_menu_items_option, $menu_items);
		return $menu_items;
	}

	/**
	 * Get the registered top-level admin toolbar (admin bar) menu items.
	 *
	 * @since 1.0.0
	 * @return array An array of admin toolbar menu items with ID, title, and optional meta.
	 */
	public function get_tb_menu_items()
	{
		global $wp_admin_bar;

		if (!is_object($wp_admin_bar) || empty($wp_admin_bar)) {
			return array();
		}

		$nodes       = $wp_admin_bar->get_nodes();
		$menu_items  = array();

		foreach ($nodes as $node) {
			if (empty($node->parent)) {

				$title = strip_tags($node->title);
				if (empty($title)) {
					continue;
				}

				$title = str_contains($title, 'Comments') ? 'Comments' : $title;

				$menu_items[] = array(
					'id'    => $node->id,
					'title' => $title,
				);
			}
		}

		update_option($this->tb_menu_items_option, $menu_items);
		return $menu_items;
	}

	/**
	 * Display a success notice after a successful scan.
	 * 
	 * This will be called in the admin_notices action
	 *
	 * @since    1.0.0
	 */
	public function display_scan_success_notice()
	{
		if (get_transient('hdmi_scan_success_notice')) {
?>
			<div class="notice notice-success is-dismissible">
				<p><?php esc_html_e('Menu scan completed successfully.', 'hide-dashboard-menu-items'); ?></p>
			</div>
<?php
			delete_transient('hdmi_scan_success_notice');
		}
	}


	/**
	 * Sanitize user inputs
	 * 
	 * @since 1.0.0
	 * 
	 * @param array $input User inputs received via admin form
	 * @return array Sanitized array of options
	 */
	public function sanitize_settings_options($input)
	{

		if (!is_array($input)) {
			return [];
		}

		$sanitized = array_filter($input, function ($item) {

			if (is_array($item)) {
				// If it's an array, sanitize each element
				return array_filter($item, function ($sub_item) {
					return is_string($sub_item) && !empty($sub_item);
				});
			} else if (is_bool($item)) {
				return $item;
			} else
				return is_string($item) && !empty($item);
		});

		if (!empty($sanitized)) return $sanitized;
	}

	/**
	 * Register the settings page for this plugin.
	 * 
	 * @since    1.0.0
	 */
	public function display_settings_page()
	{
		// Check if the user has the required capability.
		if (!current_user_can('manage_options')) {
			return;
		}

		// Include the settings page template.
		include_once plugin_dir_path(__FILE__) . 'partials/hide-dashboard-menu-items-admin-display.php';
	}

	/**
	 * Function to hide Dashboard Menu items.
	 *
	 * @since    1.0.0
	 */
	public function hide_db_menu_items()
	{
		$settings = get_option($this->settings_option, array());

		$db_hidden = $settings[$this->hidden_db_menus_key] ?? array();

		if (!is_array($db_hidden) || empty($db_hidden)) {
			return;
		}

		$bypass_active = $this->bypass_key_valid();

		if (!$bypass_active) {
			// No access — remove the menu items
			foreach ($db_hidden as $slug) {
				remove_menu_page($slug);
			}
			return;
		}

		// If access is allowed, append the bypass query to menu URLs
		$bypass_key = $settings[$this->bypass_query_key] ?? '';
		if (!$bypass_key) {
			return;
		}

		global $menu;

		$this->update_db_menu_item_slugs($menu, $db_hidden, $bypass_key);
	}

	/**
	 * Function to hide Admin Toolbar items.
	 *
	 * @since 1.0.0
	 */
	public function hide_tb_menu_items()
	{
		global $wp_admin_bar;

		$settings = get_option($this->settings_option, array());

		$tb_hidden = $settings[$this->hidden_tb_menus_key] ?? array();

		if (!is_array($tb_hidden) || empty($tb_hidden)) {
			return;
		}

		$bypass_active = $this->bypass_key_valid();
		$bypass_key = $settings[$this->bypass_query_key] ?? '';
		error_log('Bypass active: ' . ($bypass_active ? 'true' : 'false') . ', Bypass key: ' . $bypass_key);

		if (!$bypass_active && !empty($bypass_key)) {
			foreach ($tb_hidden as $id) {
				$wp_admin_bar->remove_menu($id);
			}
			return;
		}

		$this->update_tb_menu_item_slugs($wp_admin_bar, $tb_hidden, $bypass_key);
	}



	/**
	 * Modify menu items URLs to append bypass key.
	 *
	 * @since    1.0.0
	 * @param array $menu The global menu array.
	 * @param array $hidden The hidden menu slugs.
	 * @param string $bypass_key The bypass query key.
	 */
	public function update_db_menu_item_slugs($menu, $hidden, $bypass_key)
	{
		foreach ($menu as $index => $menu_item) {
			if (in_array($menu_item[2], $hidden, true)) {
				if (strpos($menu[$index][2], $bypass_key) === false) {
					if (strpos($menu[$index][2], '?') !== false) {
						$menu[$index][2] .= '&' . $bypass_key;
					} else {
						$menu[$index][2] .= '?' . $bypass_key;
					}
				}
			}
		}
	}


	/**
	 * Append the bypass query parameter to admin toolbar menu item URLs.
	 *
	 * @param WP_Admin_Bar $wp_admin_bar
	 * @param array        $hidden_slugs
	 * @param string       $bypass_key
	 */
	public function update_tb_menu_item_slugs($wp_admin_bar, $hidden_slugs, $bypass_key)
	{
		if (empty($bypass_key) || !is_array($hidden_slugs)) {
			error_log('Bypass key is empty or hidden slugs are not an array.');
			return;
		}

		error_log('Updating toolbar menu items with bypass key: ' . $bypass_key);

		foreach ($hidden_slugs as $slug) {
			$node = $wp_admin_bar->get_node($slug);

			if ($node && isset($node->href)) {
				if (strpos($node->href, $bypass_key) === false) {
					$updated_href = add_query_arg($bypass_key, '1', $node->href);
					$node->href = $updated_href;
					$wp_admin_bar->add_menu($node);
				}
			}
		}
	}


	/**
	 * Check if the bypass key is enabled and if the current request matches the bypass query.
	 *
	 * @since    1.0.0
	 * @return   bool    True if bypass is enabled and query matches, false otherwise.
	 */
	public function bypass_key_valid()
	{
		$settings = get_option($this->settings_option, array());

		if (
			!empty($settings[$this->bypass_enabled_key]) &&
			!empty($settings[$this->bypass_query_key]) &&
			isset($_SERVER['REQUEST_URI']) &&
			strpos($_SERVER['REQUEST_URI'], $settings[$this->bypass_query_key]) !== false
		) {
			return true;
		}

		return false;
	}

	/**
	 * Function to restrict access to hidden menu items.
	 *
	 * @since    1.0.0
	 */
	public function restrict_hidden_menu_access()
	{

		$settings = get_option($this->settings_option, array());
		$hidden_db_menu = $settings[$this->hidden_db_menus_key] ?? array();
		$hidden_tb_menu = $settings[$this->hidden_tb_menus_key] ?? array();

		if (empty($hidden_db_menu) && empty($hidden_tb_menu)) {
			return;
		}

		$hidden_all = array_merge($hidden_db_menu, $hidden_tb_menu);

		$current_screen = isset($_GET['page']) ? sanitize_text_field($_GET['page']) : basename($_SERVER['PHP_SELF']);

		foreach ($hidden_all as $slug) {
			if (
				strpos($_SERVER['REQUEST_URI'], $slug) !== false
				|| $current_screen === $slug
			) {
				// If the bypass is enabled, allow access
				if ($this->bypass_key_valid()) {
					return; // Allow access
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
		// load styles only in plugin admin settings page
		if ($hook_suffix !== $this->settings_page_hook_suffix) {
			return;
		}

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/hide-dashboard-menu-items-admin.css', array(), filemtime(plugin_dir_path(__FILE__) . 'css/hide-dashboard-menu-items-admin.css'), 'all');
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
