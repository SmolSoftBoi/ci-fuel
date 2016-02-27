<?php
/**
 *  @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 *  @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 *  @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Events library.
 */
class Events {

	/**
	 * @var CI_Controller CodeIgniter instance.
	 */
	protected $CI;

	/**
	 * @var array Events.
	 */
	private $events = array();

	/**
	 * @var array Objects.
	 */
	private $objects = array();

	/**
	 * @var bool In progress.
	 */
	private $in_progress = FALSE;

	/**
	 * Events constructor.
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		foreach ($this->CI->load->get_package_paths() as $package_path)
		{
			if (file_exists($package_path . 'config/events.php')) include($package_path . 'config/events.php');

			if (file_exists($package_path . 'config/' . ENVIRONMENT . '/events.php')) include($package_path . 'config/' . ENVIRONMENT . '/events.php');
		}

		if (file_exists(APPPATH . 'config/events.php')) include(APPPATH . 'config/events.php');

		if (file_exists(APPPATH . 'config/' . ENVIRONMENT . '/events.php')) include(APPPATH . 'config/' . ENVIRONMENT . '/events.php');

		if ( ! isset($event) || ! is_array($event)) return;

		$this->events =& $event;

		log_message('info', 'Events Library Initialized');
	}

	/**
	 * Calls a particular event.
	 *
	 * @param       $class   Class name.
	 * @param       $event   Event name.
	 * @param array ...$data Event data.
	 *
	 * @return mixed
	 */
	public function call_event($class, $event, ...$data)
	{
		if ( ! isset($this->events[$class][$event])) return;

		if ( ! isset($this->events[$class][$event][0]))
		{
			$this->events[$class][$event] = array($this->events[$class][$event]);
		}

		$result = array();

		foreach ($this->events[$class][$event] as $run_event)
		{
			$run_result = $this->run_event($run_event, ...$data);

			if ( ! is_null($run_result)) array_push($result, $run_result);
		}

		return $result;
	}

	/**
	 * Runs a particular event.
	 *
	 * @param string $event Event name.
	 * @param array  ...$data
	 *
	 * @return mixed
	 */
	private function run_event($event, ...$data)
	{
		if ($this->in_progress) return;

		if ( ! isset($event['filepath'])) return;

		$filepath = APPPATH . $event['filepath'];

		if ( ! file_exists($filepath)) return;

		$class = $event['class'];
		$function = $event['function'];

		$this->in_progress = TRUE;

		if (isset($this->objects[$class]))
		{
			if (method_exists($this->objects[$class], $function))
			{
				$result = $this->objects[$class]->$function(...$data);
			}
			else
			{
				$this->in_progress = FALSE;

				return;
			}
		}
		else
		{
			if ( ! class_exists($class, FALSE)) require($filepath);

			if ( ! class_exists($class, FALSE) || ! method_exists($class, $function))
			{
				$this->in_progress = FALSE;

				return;
			}

			$this->objects[$class] = new $class();

			$result = $this->objects[$class]->$function(...$data);
		}

		$this->in_progress = FALSE;

		return $result;
	}
}