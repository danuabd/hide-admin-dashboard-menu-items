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
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}
class Hide_Dashboard_Menu_Items_Admin
{

	/**
	 * ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    ID of this plugin.
	 */
	private $plugin_name = HDMI_PLUGIN_NAME;

	/**
	 * Version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    current version of this plugin.
	 */
	private $version = HDMI_VERSION;

	/**
	 * Instance of admin settings class of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Hide_Dashboard_Menu_Items_Admin_Settings    $settings_manager	Instance of admin settings class of this plugin.
	 */
	public $settings_manager;

	/**
	 * Instance of scanner class of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Hide_Dashboard_Menu_Items_Scanner    $scanner   Instance of scanner class of this plugin.
	 */
	public $scanner;

	/**
	 * Instance of debugger class of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Hide_Dashboard_Menu_Items_Debugger    $debugger    Instance of debugger class of this plugin.
	 */
	public $debugger;

	/**
	 * Instance of notice manager class of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Hide_Dashboard_Menu_Items_Notice_Manager    $notice_manager    Instance of notice manager class of this plugin.
	 */
	public $notice_manager;

	/**
	 * Instance of access manager class of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Hide_Dashboard_Menu_Items_Access_Manager    $access_manager    Instance of access manager class of this plugin.
	 */
	public $access_manager;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since	1.0.0
	 * @param	string	$plugin_name	The name of this plugin.
	 * @param	string	$version		The version of this plugin.
	 */
	public function __construct()
	{
		$this->load_dependencies();
	}

	/**
	 * Load required classes as dependencies.
	 * 
	 * @since	1.0.0
	 * @access	protected
	 */
	private function load_dependencies()
	{
		$file_path = plugin_dir_path(__FILE__);
		require_once $file_path . 'class-settings-manager.php';
		require_once $file_path . 'class-debugger.php';
		require_once $file_path . 'class-scanner.php';
		require_once $file_path . 'class-notice-manager.php';
		require_once $file_path . 'class-access-manager.php';
		require_once $file_path . 'class-storage-manager.php';

		$this->debugger = new Hide_Dashboard_Menu_Items_Debugger();

		$this->notice_manager = new Hide_Dashboard_Menu_Items_Notice_Manager();

		$this->settings_manager = new Hide_Dashboard_Menu_Items_Admin_Settings($this->debugger, $this->notice_manager);

		$this->scanner = new Hide_Dashboard_Menu_Items_Scanner($this->debugger, $this->notice_manager);

		$this->access_manager   = new Hide_Dashboard_Menu_Items_Access_Manager(
			$this->debugger,
		);
	}
}
