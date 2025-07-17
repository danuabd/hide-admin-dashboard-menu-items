<?php

/**
 * The core plugin class.
 *
 * This is used to define admin-specific hooks and
 * admin dashboard-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/includes
 * @author     ABD Prasad <contact@danukaprasasd.com>
 */
class Hide_Dashboard_Menu_Items
{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hide_Dashboard_Menu_Items_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area
	 *
	 * @since    1.0.0
	 */
	public function __construct()
	{
		if (defined('HIDE_DASHBOARD_MENU_ITEMS_VERSION')) {
			$this->version = HIDE_DASHBOARD_MENU_ITEMS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'hide-dashboard-menu-items';

		$this->load_dependencies();
		$this->define_admin_hooks();

		date_default_timezone_set('Asia/Colombo');
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hide_Dashboard_Menu_Items_Loader. Orchestrates the hooks of the plugin.
	 * - Hide_Dashboard_Menu_Items_Admin. Defines all hooks for the admin area.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies()
	{

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-hide-dashboard-menu-items-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-hide-dashboard-menu-items-admin.php';

		$this->loader = new Hide_Dashboard_Menu_Items_Loader();
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks()
	{

		$plugin_admin = new Hide_Dashboard_Menu_Items_Admin($this->get_plugin_name(), $this->get_version());

		// Fires as an admin screen or script is being initialized.
		$this->loader->add_action('admin_init', $plugin_admin->settings_manager, 'register_settings', 10);
		$this->loader->add_action('admin_init', $plugin_admin->access_manager, 'restrict_menu_access');

		// Fires before the administration menu loads in the admin.
		$this->loader->add_action('admin_menu', $plugin_admin->scanner, 'scan_dashboard_menu', 10);
		$this->loader->add_action('admin_menu', $plugin_admin->settings_manager, 'add_admin_menu');
		$this->loader->add_action('admin_menu', $plugin_admin->access_manager, 'hide_dashboard_menu');

		// Loads all necessary admin bar items.
		$this->loader->add_action('admin_bar_menu', $plugin_admin->scanner, 'scan_toolbar_menu', 10);
		$this->loader->add_action('wp_before_admin_bar_render', $plugin_admin->access_manager, 'hide_toolbar_menu');

		// Prints admin screen notices.
		$this->loader->add_action('admin_notices', $plugin_admin->notice_manager, 'render_notices');

		// Runs in the HTML header so a plugin or theme can enqueue JavaScript and CSS to all admin pages.
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin->settings_manager, 'enqueue_styles');
		$this->loader->add_action('admin_enqueue_scripts', $plugin_admin->settings_manager, 'enqueue_scripts');
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run()
	{
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name()
	{
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hide_Dashboard_Menu_Items_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader()
	{
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version()
	{
		return $this->version;
	}
}
