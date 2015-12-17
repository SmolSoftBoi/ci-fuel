<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function cdn_url($uri = '', $protocol = NULL)
{
	$CI =& get_instance();

	$CI->load->library('storage');

	$CI->storage->cdn_url($uri, $protocol);
}