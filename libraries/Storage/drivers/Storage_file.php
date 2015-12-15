<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storage_file extends Storage_driver {

	public function initialize()
	{
		$this->CI->load->helper('file');
	}

	public function write_file($path, $data, $params = NULL)
	{
		if ( ! write_file($this->path . '/' . $path, $data)) return FALSE;

		$this->url = $this->public_url . $path;

		return TRUE;
	}
}