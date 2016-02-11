<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('cdn_url'))
{
	function cdn_url($uri = '', $protocol = NULL)
	{
		$CI =& get_instance();

		$CI->load->driver('storage');

		return $CI->storage->cdn_url($uri, $protocol);
	}
}