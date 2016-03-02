<?php
/**
 *  @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 *  @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 *  @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication driver.
 *
 * @package CodeIgniter Fuel\Libraries\Auth
 */
abstract class Auth_driver extends CI_Driver {

	/**
	 * @var string Session prefix.
	 */
	public $session_prefix;

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * Authentication driver constructor.
	 *
	 * @param array $params Parameters.
	 */
	public function __construct($params)
	{
		$this->CI =& get_instance();

		if (is_array($params))
		{
			foreach ($params as $key => $val)
			{
				$this->$key = $val;
			}
		}

		log_message('info', 'Storage Driver Initialized');
	}

	/**
	 * Authentication driver initialize.
	 *
	 * @return bool
	 */
	public function initialize()
	{
		return TRUE;
	}

	/**
	 * Verify and sign in.
	 *
	 * @param mixed $credentials Credentials.
	 * @param bool  $remember    Remember.
	 */
	public function sign_in($credentials, $remember = FALSE)
	{

	}

	/**
	 * Sign in by user ID.
	 *
	 * @param int  $user_id  User ID.
	 * @param bool $remember Remember.
	 */
	public function sign_in_by_user_id($user_id, $remember = FALSE)
	{

	}

	/**
	 * Verify and sign in once.
	 *
	 * @param mixed $credentials Credentials.
	 */
	public function sign_in_once($credentials)
	{

	}

	/**
	 * Sign in once by user ID.
	 *
	 * @param int $user_id User ID.
	 */
	public function sign_in_once_by_user_id($user_id)
	{

	}

	/**
	 * Sign out.
	 */
	public function sign_out()
	{
		unset($_SESSION);
	}

	/**
	 * Validate credentials.
	 *
	 * @param mixed $credentials Credentials.
	 */
	public function validate($credentials)
	{

	}

	/**
	 * Authenticate.
	 *
	 * @param array $userdata Userdata.
	 */
	protected function auth($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed' => TRUE
		));

		foreach ($userdata as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Authenticate guest.
	 *
	 * @param array $userdata Userdata.
	 */
	protected function auth_guest($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed' => TRUE,
			$this->session_prefix . 'guest'  => TRUE
		));

		foreach ($userdata as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Authenticate by remember.
	 *
	 * @param array $userdata Userdata.
	 */
	protected function auth_by_remember($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed'   => TRUE,
			$this->session_prefix . 'remember' => TRUE
		));

		foreach ($userdata as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}

	/**
	 * Authenticate guest by remember.
	 *
	 * @param array $userdata Userdata.
	 */
	protected function auth_guest_by_remember($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed'   => TRUE,
			$this->session_prefix . 'guest'    => TRUE,
			$this->session_prefix . 'remember' => TRUE
		));

		foreach ($userdata as $key => $value)
		{
			$_SESSION[$key] = $value;
		}
	}
}