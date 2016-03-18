<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fakers.
 */
class Fakers {

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * Fakers constructor.
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->helper(array('directory', 'file'));

		log_message('info', 'Fakers library initialized.');
	}

	/**
	 * Autoload.
	 *
	 * @param string $class Class.
	 */
	public static function autoload($class)
	{
		$CI =& get_instance();

		$CI->load->helper('file');

		foreach ($CI->load->get_package_paths() as $package_path)
		{
			if (file_exists($package_path . 'fakers/' . $class . '.php'))
			{
				require_once $package_path . 'fakers/' . $class . '.php';
			}

			foreach (directory_map($package_path . 'fakers/') as $directory_path)
			{
				if (file_exists($directory_path . $class . '.php'))
				{
					require_once $directory_path . $class . '.php';
				}
			}
		}
	}
}

spl_autoload_register('Fakers::autoload');