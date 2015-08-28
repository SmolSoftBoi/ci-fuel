<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function put_csv($fields)
{
	$fp = fopen('php://temp', 'r+');

	fputcsv($fp, $fields);

	rewind($fp);

	$output = fgets($fp);

	fclose($fp);

	return $output;
}