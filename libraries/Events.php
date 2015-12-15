<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Events library
 */
class Events {

	/**
	 * List of all events
	 */
	private $events = array();

	protected $CI;

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->CI =& get_instance();

		log_message('debug', 'Events Library Initialized');
	}

	/**
	 * Setter
	 */
	public function __set($name, $value)
	{
		if ( ! isset($this->events[$name])) $this->events[$name] = array();

		array_push($this->events[$name], $value);
	}

	/**
	 * Is setter
	 */
	public function __isset($name)
	{
		return isset($this->events[$name]);
	}

	/**
	 * Unsetter
	 */
	public function __unset($name)
	{
		unset($this->events[$name]);
	}

	/**
	 * Calls a particular event
	 */
	public function call_event($event = NULL, ...$data)
	{
		if (is_null($event)) return;

		if ( ! isset($this->events[$event])) return;

		foreach ($this->events[$event] as $event)
		{
			$result = $this->run_event($event, $data);
		}

		return $result;
	}

	/**
	 * Runs a particular event
	 */
	private function run_event($event, $data)
	{
		if (is_callable($event)) return $event(...$data);
	}
}