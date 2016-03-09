<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('exec_bg'))
{
	function exec_bg($command)
	{
		exec($command . ' >/dev/null 2>&1');
	}
}