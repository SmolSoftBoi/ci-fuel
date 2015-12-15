<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Storage extends CI_DB_driver {

	protected $CI;

	private $driver;

	/**
	 * Constructor
	 */
	public function __construct($params = NULL)
	{
		$this->CI =& get_instance();

		if ( ! file_exists($config_path = APPPATH . 'config/' . ENVIRONMENT . '/storage.php') && ! file_exists($config_path = APPPATH . 'config/storage.php'))
		{
			show_error('The configuration file storage.php does not exist.');
		}

		include($config_path);

		foreach ($this->CI->load->get_package_paths() as $package_path)
		{
			if ($package_path !== APPPATH)
			{
				if (file_exists($config_path = $package_path . 'config/' . ENVIRONMENT . '/storage.php'))
				{
					include($config_path);
				}
				else if (file_exists($config_path = $package_path . 'config/storage.php'))
				{
					include($config_path);
				}
			}
		}

		if ( ! isset($storage) || count($storage) === 0)
		{
			show_error('No storage settings were found in the storage config file.');
		}

		if ( ! is_null($params))
		{
			$active_group = $params;
		}

		if ( ! isset($active_group))
		{
			show_error('You have not specified a storage group via $active_group in your config/storage.php file.');
		}
		else if ( ! isset($storage[$active_group]))
		{
			show_error('You have specified an invalid storage group (' . $active_group . ') in your config/storage.php file.');
		}

		$params = $storage[$active_group];

		if (empty($params['driver']))
		{
			show_error('You have not selected a storage type.');
		}

		require_once(dirname(__FILE__) . '/Storage_driver.php');

		$driver_path = dirname(__FILE__) . '/drivers/Storage_' . $params['driver'] . '.php';

		file_exists($driver_path) || show_error('Invalid storage driver');

		require_once($driver_path);

		$driver = 'Storage_' . $params['driver'];

		$storage = new $driver($params);

		$storage->initialize();

		$this->driver = $storage;

		return $storage;
	}

	/**
	 * Caller
	 */
	public function __call($name, $arguments)
	{
		return $this->driver->$name(...$arguments);
	}

	/**
	 * Getter
	 */
	public function __get($name)
	{
		return $this->driver->$name;
	}
}