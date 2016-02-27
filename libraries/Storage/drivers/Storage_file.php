<?php
/**
 *  @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 *  @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 *  @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Storage_file extends Storage_driver {

	public function initialize()
	{
		$this->CI->load->helper('file');
	}

	public function write_file($path, $data, $params = NULL)
	{
		$paths = explode('/', $this->path . '/' . $path);

		array_pop($paths);

		$paths = implode('/', $paths);

		if ( ! is_dir($paths)) mkdir($paths);

		if ( ! write_file($this->path . '/' . $path, $data)) return FALSE;

		$this->url = $this->public_url . $path;

		return TRUE;
	}
}