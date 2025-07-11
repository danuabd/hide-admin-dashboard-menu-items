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
	 * @var      string    $menu_items_option    The name of the option where menu items are cached.
	 */
	private $menu_items_option;

	/**
	 * The key of the option where hidden menus are stored.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hidden_menus_key    The key of the option where hidden menus are stored.
	 */
	private $hidden_menus_key;

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
		$this->settings_option = $this->plugin_name . '_settings';
		$this->menu_items_option = $this->plugin_name . '_cached_menu_items';
		$this->scan_success_option = '_scan_completed';
		$this->hidden_menus_key = '_hidden_menus';

		// Define the slugs for the settings and debug pages
		$this->settings_page_slug = $this->plugin_name . '-settings';
		$this->debug_page_slug = $this->plugin_name . '-debug';
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
			$menu_items = $this->get_registered_top_level_admin_menu_items();

			// Store in DB
			update_option($this->menu_items_option, $menu_items);
			update_option($this->scan_success_option, 1);

			// Redirect back with success transient
			set_transient('hdmi_scan_success_notice', true, 30);
			wp_redirect(admin_url('admin.php?page=' . $this->settings_page_slug));
			exit;
		}
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
			$this->plugin_name . '_group',
			$this->settings_option,
			array($this, 'sanitize_settings_options')
		);
	}

	public function register_fields_and_sections()
	{
		add_settings_section(
			$this->plugin_name . '_settings_section',
			'',
			function () {
				echo '';
			},
			$this->settings_page_slug
		);
	}

	/**
	 * Get the registered top-level admin menu items.
	 *
	 * @since    1.0.0
	 * @return   array    An array of top-level admin menu items.
	 */
	public function get_registered_top_level_admin_menu_items()
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

		return $menu_items;
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
		/**
		 * To hold sanitized data
		 */
		$sanitized_data = array();

		return $sanitized_data;
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

		wp_enqueue_style($this->plugin_name, plugin_dir_url(__FILE__) . 'css/hide-dashboard-menu-items-admin.css', array(), $this->version, 'all');
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

		wp_enqueue_script($this->plugin_name, plugin_dir_url(__FILE__) . 'js/hide-dashboard-menu-items-admin.js', array(), $this->version, false);
	}
}
