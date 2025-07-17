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

	private $config;

	private $option_manager;

	public $settings;

	public $scanner;

	public $debugger;

	public $notices;

	public $access_manager;

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
		$this->load_dependencies();
	}

	private function load_dependencies()
	{
		require_once plugin_dir_path(__FILE__) . 'class-config.php';
		require_once plugin_dir_path(__FILE__) . 'class-settings.php';
		require_once plugin_dir_path(__FILE__) . 'class-debugger.php';
		require_once plugin_dir_path(__FILE__) . 'class-scanner.php';
		require_once plugin_dir_path(__FILE__) . 'class-notices.php';
		require_once plugin_dir_path(__FILE__) . 'class-access-manager.php';
		require_once plugin_dir_path(__FILE__) . 'helpers/class-option-manager.php';

		$this->config = new Hide_Dashboard_Menu_Items_Config($this->plugin_name, $this->version);
		$this->option_manager = new Hide_Dashboard_Menu_Items_Options($this->config->option_name);
		$this->settings = new Hide_Dashboard_Menu_Items_Admin_Settings($this->config, $this->debugger, $this->notices);
		$this->scanner = new Hide_Dashboard_Menu_Items_Scanner($this->config, $this->option_manager, $this->debugger, $this->notices);
		$this->debugger = new Hide_Dashboard_Menu_Items_Debugger($this->config, $this->debugger);
		$this->notices  = new Hide_Dashboard_Menu_Items_Notices();
		$this->access_manager   = new Hide_Dashboard_Menu_Items_Access_Manager(
			$this->config,
			$this->$option_manager,
			$this->debugger,
			$this->notices
		);
	}
}
