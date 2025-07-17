<?php

/**
 * Helper class for managing options of this plugin.
 *
 *
 * @link       https://danukaprasad.com
 * @since      1.0.0
 *
 * @package    Hide_Dashboard_Menu_Items
 * @subpackage Hide_Dashboard_Menu_Items/admin/helpers
 */
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
class Hide_Dashboard_Menu_Items_Options
{
    private $option_name;

    public function __construct($option_name)
    {
        $this->option_name = $option_name;
    }

    public function get($key = null, $default = null)
    {
        $options = get_option($this->option_name, array());

        if ($key === null) {
            return $options;
        }

        return isset($options[$key]) ? $options[$key] : $default;
    }

    public function update($key, $value)
    {
        $options = get_option($this->option_name, array());
        $options[$key] = $value;
        update_option($this->option_name, $options);
    }
}
