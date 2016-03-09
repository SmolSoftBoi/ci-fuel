<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Domain.
 */
class Domain {

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * Domain constructor.
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->helper('file');

		log_message('info', 'Domain library initialized.');
	}

	/**
	 * Autoload.
	 *
	 * @param string $class Class.
	 */
	static public function autoload($class)
	{
		$CI =& get_instance();

		$CI->load->helper('file');

		foreach ($CI->load->get_package_paths() as $package_path)
		{
			if (file_exists($package_path . 'domain/' . $class . '.php'))
			{
				require_once $package_path . 'domain/' . $class . '.php';
			}
		}
	}
}

spl_autoload_register('Domain::autoload');