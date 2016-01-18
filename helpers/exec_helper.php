<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('exec_bg'))
{
	function exec_bg($command)
	{
		exec($command . ' >/dev/null 2>&1');
	}
}