<?php
/**
 * Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication library
 */
class Auth extends CI_Driver_Library {

	protected $CI;

	private $driver;

	private $config;

	private $timestamp;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		$this->CI->load->library(array('events', 'session'));
		$this->CI->load->helper('date');

		if ( ! file_exists($config_path = APPPATH . 'config/' . ENVIRONMENT . '/auth.php') && ! file_exists($config_path = APPPATH . 'config/auth.php'))
		{
			show_error('The configuration file auth.php does not exist.');
		}

		$this->config = $this->CI->config->load('auth', TRUE)['auth'];

		$this->timestamp = now();

		require_once(dirname(__FILE__) . '/Storage_driver.php');

		$driver_path = dirname(__FILE__) . '/drivers/Auth_auth.php';

		file_exists($driver_path) || show_error('Invalid authentication driver');

		require_once($driver_path);

		$auth = new 'Auth_auth'($params);

		$auth->initialize();

		$this->driver = $auth;

		log_message('debug', 'Authentication Library Initialized');
	}

	/**
	 * Is authed
	 */
	public function authed()
	{
		if ($this->CI->session->userdata($this->config['session_prefix'] . 'authed') === TRUE) return TRUE;

		return FALSE;
	}

	/**
	 * Is authed guest
	 */
	public function authed_guest()
	{
		if ($this->authed() && $this->CI->session->userdata($this->config['session_prefix'] . 'guest') === TRUE) return TRUE;

		return FALSE;
	}

	/**
	 * Is authed by remember
	 */
	public function authed_by_remember()
	{
		if ($this->authed() && $this->CI->session->userdata($this->config['session_prefix'] . 'remember') === TRUE) return TRUE;

		return FALSE;
	}

	/**
	 * Is authed guest by remember
	 */
	public function authed_guest_by_remember()
	{
		if ($this->authed_guest() && $this->authed_by_remember()) return TRUE;

		return FALSE;
	}

	/**
	 * Is authed by role
	 */
	public function authed_by_role($roles)
	{
		if ( ! is_array($roles)) $roles = array($roles);

		foreach ($roles as $role) if ($this->authed() && in_array($role, $this->CI->session->userdata($this->config['session_prefix'] . 'roles'))) return TRUE;

		return FALSE;
	}

	public function authed_by_group($group)
	{
		if ($this->authed() && in_array($group, $this->CI->session->userdata($this->config['session_prefix'] . 'groups'))) return TRUE;

		return FALSE;
	}

	/**
	 * Verify and sign in
	 */
	public function sign_in($credentials, $remember = FALSE)
	{
		return $this->driver->sign_in($credentials, $remember);
	}

	/**
	 * Sign in by user ID
	 */
	public function sign_in_by_user_id($user_id, $remember = FALSE)
	{
		return $this->driver->sign_in_by_user_id($user_id, $remember);
	}

	/**
	 * Verify and sign in once
	 */
	public function sign_in_once($credentials)
	{
		return $this->driver->sign_in_once($credentials);
	}

	/**
	 * Sign in once by user ID
	 */
	public function sign_in_once_by_user_id($user_id)
	{
		return $this->driver->sign_in_once_by_user_id($user_id);
	}

	/**
	 * Sign out
	 */
	public function sign_out()
	{
		$this->driver->sign_out();
	}

	/**
	 * Validate credentials
	 */
	public function validate($credentials)
	{
		return $this->driver->validate($credentials);
	}
}