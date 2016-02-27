<?php
/**
 *  @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 *  @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 *  @package   CodeIgniter Fuel\Libraries\Auth
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Authentication library.
 */
class Auth extends CI_Driver_Library {

	/**
	 * @var array $config Configuration.
	 */
	public $config = array(
		'uri' => NULL,
		'session_prefix' => NULL
	);

	/**
	 * @var CI_Controller $CI CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * @var string[] $valid_drivers Valid drivers.
	 */
	protected $valid_drivers = array('default');

	/**
	 * @var Auth_driver $driver Authentication driver.
	 */
	private $driver;

	/**
	 * @var int $timestamp Timestamp.
	 */
	private $timestamp;

	/**
	 * Authentication library constructor.
	 *
	 * @param array() $params Parameters.
	 */
	public function __construct($params = array())
	{
		$this->CI =& get_instance();

		$this->CI->load->library('session');
		$this->CI->load->helper(array('date', 'url'));

		$this->timestamp = now();

		$this->check_config();

		if ($this->CI->config->load('auth', TRUE))
		{
			$this->config = array_merge($this->config, $this->CI->config->item('auth'));
		}

		require_once 'Auth_driver.php';

		$this->driver = $this->load_driver('default');

		$this->driver->initialize($params);

		log_message('debug', 'Authentication library initialized.');
	}

	/**
	 * Authentication library initialize.
	 *
	 * @param array() $params Parameters.
	 */
	public function initialize($params)
	{
		if (isset($params['valid_drivers']))
		{
			if (is_array($params))
			{
				$params['valid_drivers'] = array_merge($this->valid_drivers, $params['valid_drivers']);
			}
		}

		if (is_array($params))
		{
			foreach ($params as $key => $value)
			{
				$this->$key = $value;
			}
		}
	}

	/**
	 * Is authed.
	 *
	 * @return bool
	 */
	public function authed($redirect = TRUE)
	{
		$events_library = $this->CI->load->is_loaded('Events');

		if ($events_library !== FALSE)
		{
			$this->CI->$events_library->call_event(__CLASS__, 'pre_authed');
		}

		if ($_SESSION[$this->config['session_prefix'] . 'authed'] === TRUE)
		{
			$authed = TRUE;
		}

		$authed = FALSE;

		if ( ! $authed && $redirect)
		{
			$this->redirect();
		}

		return $authed;
	}

	/**
	 * Is authed guest.
	 *
	 * @return bool
	 */
	public function authed_guest($redirect = TRUE)
	{
		$events_library = $this->CI->load->is_loaded('Events');

		if ($events_library !== FALSE)
		{
			$this->CI->$events_library->call_event(__CLASS__, 'pre_authed_guest');
		}

		if ($this->authed($redirect)
		    && $_SESSION[$this->config['session_prefix'] . 'guest'] === TRUE
		)
		{
			$authed = TRUE;
		}

		$authed = FALSE;

		if ( ! $authed && $redirect)
		{
			$this->redirect();
		}

		return $authed;
	}

	/**
	 * Is authed by remember.
	 *
	 * @return bool
	 */
	public function authed_by_remember($redirect = TRUE)
	{
		$events_library = $this->CI->load->is_loaded('Events');

		if ($events_library !== FALSE)
		{
			$this->CI->$events_library->call_event(__CLASS__, 'pre_authed_by_remember');
		}

		if ($this->authed($redirect)
		    && $_SESSION[$this->config['session_prefix'] . 'remember'] === TRUE
		)
		{
			$authed = TRUE;
		}

		$authed = FALSE;

		if ( ! $authed && $redirect)
		{
			$this->redirect();
		}

		return $authed;
	}

	/**
	 * Is authed guest by remember.
	 *
	 * @return bool
	 */
	public function authed_guest_by_remember($redirect = TRUE)
	{
		$events_library = $this->CI->load->is_loaded('Events');

		if ($events_library !== FALSE)
		{
			$this->CI->$events_library->call_event(__CLASS__, 'pre_authed_guest_by_remember');
		}

		if ($this->authed_guest($redirect) && $this->authed_by_remember($redirect))
		{
			$authed = TRUE;
		}

		$authed = FALSE;

		if ( ! $authed && $redirect)
		{
			$this->redirect();
		}

		return $authed;
	}

	/**
	 * Is authed by role.
	 *
	 * @param string[] $roles Roles.
	 *
	 * @return bool
	 */
	public function authed_by_role($roles, $redirect = TRUE)
	{
		$events_library = $this->CI->load->is_loaded('Events');

		if ($events_library !== FALSE)
		{
			$this->CI->$events_library->call_event(__CLASS__, 'pre_authed_by_role', $roles);
		}

		if ( ! is_array($roles))
		{
			$roles = array($roles);
		}

		foreach ($roles as $role)
		{
			if ($this->authed($redirect)
			    && in_array($role, $_SESSION[$this->config['session_prefix'] . 'roles'])
			)
			{
				return TRUE;
			}
		}

		return FALSE;
	}

	/**
	 *
	 * Is authed by group.
	 *
	 * @param string $group Group.
	 *
	 * @return bool
	 */
	public function authed_by_group($group, $redirect = TRUE)
	{
		$events_library = $this->CI->load->is_loaded('Events');

		if ($events_library !== FALSE)
		{
			$this->CI->$events_library->call_event(__CLASS__, 'pre_authed_by_group', $group5);
		}

		if ($this->authed()
		    && in_array($group, $_SESSION[$this->config['session_prefix'] . 'groups'])
		)
		{
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Verify and sign in.
	 *
	 * @param mixed $credentials Credentials.
	 * @param bool  $remember    Remember.
	 *
	 * @return mixed
	 */
	public function sign_in($credentials, $remember = FALSE)
	{
		return $this->driver->sign_in($credentials, $remember);
	}

	/**
	 * Sign in by user ID.
	 *
	 * @param int  $user_id  User ID.
	 * @param bool $remember Remember.
	 *
	 * @return mixed
	 */
	public function sign_in_by_user_id($user_id, $remember = FALSE)
	{
		return $this->driver->sign_in_by_user_id($user_id, $remember);
	}

	/**
	 * Verify and sign in once.
	 *
	 * @param mixed $credentials Credentials.
	 *
	 * @return mixed
	 */
	public function sign_in_once($credentials)
	{
		return $this->driver->sign_in_once($credentials);
	}

	/**
	 * Sign in once by user ID
	 *
	 * @param int $user_id User ID.
	 *
	 * @return mixed
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
	 *
	 * @param mixed $credentials Credentials.
	 *
	 * @return bool
	 */
	public function validate($credentials)
	{
		return $this->driver->validate($credentials);
	}

	/**
	 * Set URI.
	 */
	public function set_uri()
	{
		$_SESSION['url'] = uri_string();
	}

	/**
	 * Get URI.
	 *
	 * @return string URI.
	 */
	public function get_uri()
	{
		if (isset($_SESSION[$this->config['session_prefix'] . 'url']))
		{
			return $_SESSION[$this->config['session_prefix'] . 'url'];
		}

		return '/';
	}

	/**
	 * Check configuration.
	 */
	private function check_config()
	{
		$config_exists = FALSE;

		foreach ($this->CI->load->get_package_paths() as $package_path)
		{
			if (file_exists($package_path . 'config/' . ENVIRONMENT . '/auth.php')
			    || file_exists($package_path . 'config/auth.php')
			)
			{
				$config_exists = TRUE;

				break;
			}
		}

		if ( ! $config_exists)
		{
			show_error('Authentication configuration does not exist.', 500, AUTH_CLASS . ' Error');
		}
	}
}