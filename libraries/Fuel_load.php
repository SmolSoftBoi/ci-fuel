<?php
/**
 * Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Fuel load.
 */
class Fuel_load {

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * Fuel load constructor.
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->library('fuel_autoload', FALSE);
		$this->CI->load->helper('file');
	}

	/**
	 * Enums.
	 *
	 * @param mixed $enums Enums.
	 */
	public function enums($enums = TRUE)
	{
		if ($enums === TRUE)
		{
			$this->CI->fuel_autoload->enums();
		}
		else
		{
			if ( ! is_array($enums)) $enums = array($enum);

			foreach ($enums as $enum)
			{
				if (file_exists($enum_path = APPPATH . 'enums' . $enum . '.php'))
				{
					include($enum_path);
				}
				else
				{
					foreach ($this->CI->load->get_package_paths() as $package_path)
					{
						if (file_exists($enum_path = $package_path . 'enums/' . $enum . '.php'))
						{
							include($enum_path);
						}
					}
				}
			}
		}
	}
}