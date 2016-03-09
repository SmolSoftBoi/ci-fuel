<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Storage_awss3 extends Storage_driver {

	public $s3;

	public function initialize()
	{
		$credentials = new Aws\Credentials\Credentials($this->key, $this->secret);

		$this->s3 = new Aws\S3\S3Client(array(
			'version'     => 'latest',
			'region'      => $this->region,
			'credentials' => $credentials
		));
	}

	public function write_file($path, $data, $params = NULL)
	{
		$object = array(
			'Bucket' => $this->path,
			'Key'    => $path,
			'Body'   => $data,
			'ACL'    => 'public-read'
		);

		if (isset($params['mime']))
		{
			$object['ContentType'] = $params['mime'];
		}

		if ( ! isset($object['ContentType']))
		{
			$mime = get_mime_by_extension($path);

			if ($mime !== FALSE)
			{
				$object['ContentType'] = $mime;
			}
		}

		$result = $this->s3->putObject($object);

		$this->url = $this->public_url . $path;

		return TRUE;
	}
}