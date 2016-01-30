<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('merge'))
{
	/**
	 * Merge one or more arrays or objects.
	 *
	 * @param mixed ...$arrays Arrays or objects.
	 *
	 * @return mixed Merged arrays or objects.
	 */
	function merge(...$arrays)
	{
		return (gettype($array)) array_merge((array) ...$arrays);
	}
}