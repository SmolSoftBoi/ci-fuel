<?php
defined('BASEPATH') OR exit('No direct script access allowed');

abstract class Auth_driver extends CI_Driver {

	public $session_prefix;

	protected $CI;

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

	public function initialize()
	{
		return TRUE;
	}

	/**
	 * Auth
	 */
	private function auth($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed') => TRUE
		));

		$this->CI->session->set_userdata($userdata);
	}

	/**
	 * Auth guest
	 */
	private function authed_guest($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed') => TRUE,
			$this->session_prefix . 'guest')  => TRUE
		));

		$this->CI->session->set_userdata($userdata);
	}

	/**
	 * Auth by remember
	 */
	private function authed_by_remember($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed')   => TRUE,
			$this->session_prefix . 'remember') => TRUE
		));

		$this->CI->session->set_userdata($userdata);
	}

	/**
	 * Auth guest by remember
	 */
	private function authed_guest_by_remember($userdata = array())
	{
		$userdata = array_merge(array(
			'roles'  => array(),
			'groups' => array()
		), $userdata, array(
			$this->session_prefix . 'authed')   => TRUE,
			$this->session_prefix . 'guest')    => TRUE,
			$this->session_prefix . 'remember') => TRUE
		));

		$this->CI->session->set_userdata($userdata);
	}

	/**
	 * Verify and sign in
	 */
	public function sign_in($credentials, $remember = FALSE)
	{

	}

	/**
	 * Sign in by user ID
	 */
	public function sign_in_by_user_id($user_id, $remember = FALSE)
	{

	}

	/**
	 * Verify and sign in once
	 */
	public function sign_in_once($credentials)
	{

	}

	/**
	 * Sign in once by user ID
	 */
	public function sign_in_once_by_user_id($user_id)
	{

	}

	/**
	 * Sign out
	 */
	public function sign_out()
	{
		$this->session->sess_destroy();
	}

	/**
	 * Validate credentials
	 */
	public function validate($credentials)
	{

	}
}