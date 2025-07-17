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
