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

	private $storage_manager;

	public $settings_manager;

	public $scanner;

	public $debugger;

	public $notice_manager;

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
		$file_path = plugin_dir_path(__FILE__);
		require_once $file_path . 'class-config.php';
		require_once $file_path . 'class-settings-manager.php';
		require_once $file_path . 'class-debugger.php';
		require_once $file_path . 'class-scanner.php';
		require_once $file_path . 'class-notice-manager.php';
		require_once $file_path . 'class-access-manager.php';
		require_once $file_path . 'class-storage-manager.php';

		$this->config = new Hide_Dashboard_Menu_Items_Config($this->plugin_name, $this->version);

		$this->storage_manager = new Hide_Dashboard_Menu_Items_Storage_Manager($this->config);

		$this->debugger = new Hide_Dashboard_Menu_Items_Debugger($this->config, $this->storage_manager);

		$this->notice_manager  = new Hide_Dashboard_Menu_Items_Notices();

		$this->settings_manager = new Hide_Dashboard_Menu_Items_Admin_Settings($this->config, $this->storage_manager, $this->debugger, $this->notice_manager);

		$this->scanner = new Hide_Dashboard_Menu_Items_Scanner($this->config, $this->storage_manager, $this->debugger, $this->notice_manager);

		$this->access_manager   = new Hide_Dashboard_Menu_Items_Access_Manager(
			$this->config,
			$this->storage_manager,
			$this->debugger,
			$this->notice_manager
		);
	}
}
