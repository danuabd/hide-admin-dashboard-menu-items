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
	 * The hook suffix for the settings page.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string
	 */
	private $settings_page_hook_suffix;

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
	}

	/**
	 * Initialize the scan for the plugin admin area. Run only once.
	 *
	 * @since    1.0.0
	 */
	public function maybe_trigger_first_time_scan()
	{
		// Only run on plugin settings page
		if (!isset($_GET['page']) || $_GET['page'] !== 'hide-dashboard-menu-items-settings') {
			return;
		}

		// Only run once
		if (get_option('hdmi_scan_completed')) {
			return;
		}

		// Redirect to trigger scan via URL param
		wp_redirect(add_query_arg('hdmi_trigger_scan', '1', admin_url('admin.php?page=hide-dashboard-menu-items-settings')));
		exit;
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
			'hide-dashboard-menu-items-settings',
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
			$this->plugin_name . '_settings',
			array($this, 'sanitize_settings_options')
		);
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
