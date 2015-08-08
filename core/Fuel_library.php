<?php
defined('BASEPATH') OR exit('No direct script access allowed');

interface FUEL_Library_Template {
	
}

class FUEL_Library implements FUEL_Library_Template {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}
}