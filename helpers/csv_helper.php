<?php
defined('BASEPATH') OR exit('No direct script access allowed');

if ( ! function_exists('put_csv'))
{
	function put_csv($fields)
	{
		$fp = fopen('php://temp', 'r+');

		fputcsv($fp, $fields);

		rewind($fp);

		$output = fgets($fp);

		fclose($fp);

		return $output;
	}
}