<?php
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
	 * @param null  $event   Event name.
	 * @param array ...$data Event data.
	 *
	 * @return mixed
	 */
	public function call_event($event = NULL, ...$data)
	{
		if (is_null($event)) return;

		if ( ! isset($this->events[$event])) return;

		foreach ($this->events[$event] as $event_item)
		{
			$run_result = $this->run_event($event_item, ...$data);

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

		if ( ! isset($event['filepath'], $event['filename'])) return;

		$filepath = APPPATH . $event['filepath'] . '/' . $event['filename'];

		if ( ! file_exists($filepath)) return;

		$this->in_progress = TRUE;

		if (isset($this->objects[$events['class']]))
		{
			if (method_exists($this->objects[$events['class']], $events['function']))
			{
				$result = $this->objects[$events['class']]->$events['function'](...$data);
			}
			else
			{
				$this->in_progress = FALSE;

				return;
			}
		}
		else
		{
			if ( ! class_exists($event['class'], FALSE)) require_once($filepath);

			if ( ! class_exists($event['class'], FALSE) || method_exists($event['class'], $event['function']))
			{
				$this->in_progress = FALSE;

				return;
			}

			$this->objects[$event['class']] = new $event['class']();

			$result = $this->objects[$event['class']]->$event['function'](...$data);
		}

		$this->in_progress = FALSE;

		return $result;
	}
}