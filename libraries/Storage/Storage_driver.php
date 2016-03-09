<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Storage_driver extends CI_Driver {

	public $base_url;
	public $path;
	public $driver;
	public $key;
	public $secret;
	public $region;

	public $url;

	protected $CI;

	public function __construct($params)
	{
		$this->CI =& get_instance();

		if (is_array($params))
		{
			foreach ($params as $key => $val)
			{
				$this->$key = $val;
			}
		}

		log_message('info', 'Storage Driver Initialized');
	}

	public function initialize()
	{
		return TRUE;
	}

	public function write_file($path, $data, $params = NULL)
	{
		$this->url = $this->public_url . $path;

		return TRUE;
	}
}