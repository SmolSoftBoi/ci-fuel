<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fuel autoload.
 */
class Fuel_autoload {

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * Fuel autoload constructor.
	 *
	 * @param bool @autoload Autoload.
	 */
	public function __construct($autoload = TRUE)
	{
		$this->CI =& get_instance();

		if ($autoload === FALSE) return;

		$this->CI->load->library(array(
			'cart',
			'enum',
			'events',
			'template'
		));
		$this->CI->load->driver(array(
			'auth',
			'storage'
		));
		$this->CI->load->helper(array(
			'csv',
			'exec',
			'file',
			'merge',
			'storage'
		));

		$this->enums();
	}

	/**
	 * Enums.
	 */
	public function enums()
	{
		$enum_paths = get_filenames(APPPATH . 'enums/', TRUE);

		if ($enum_paths === FALSE) $enum_paths = array();

		foreach ($enum_paths as $enum_path) include($enum_path);

		foreach ($this->CI->load->get_package_paths() as $package_path)
		{
			$enum_paths = get_filenames($package_path . 'enums/', TRUE);

			if ($enum_paths === FALSE) $enum_paths = array();

			foreach ($enum_paths as $enum_path) include($enum_path);
		}
	}
}