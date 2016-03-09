<?php
/**
 * @copyright Copyright © 2015 - 2016 Kristian Matthews. All rights reserved.
 * @author    Kristian Matthews <kristian.matthews@my.westminster.ac.uk>
 * @package   CodeIgniter Fuel
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Template {

	protected $CI;

	public function __construct()
	{
		$this->CI =& get_instance();
	}

	public function view($template_view, $body_view = NULL, $vars = array(), $return = FALSE)
	{
		if ( ! is_null($body_view))
		{
			if (file_exists(APPPATH . 'views/' . $template_view . '/' . $body_view))
			{
				$body_view_path = $template_view . '/' . $body_view;
			}
			else if (file_exists(APPPATH . 'views/' . $template_view . '/' . $body_view . '.php'))
			{
				$body_view_path = $template_view . '/' . $body_view . '.php';
			}
			else if (file_exists(APPPATH . 'views/' . $body_view))
			{
				$body_view_path = $body_view;
			}
			else if (file_exists(APPPATH . 'views/' . $body_view . '.php'))
			{
				$body_view_path = $body_view . '.php';
			}
			else
			{
				show_error('Unable to load the requested file: ' . $template_view . '/' . $body_view . '.php');
			}

			$body = $this->CI->load->view($body_view_path, $vars, TRUE);

			if ( ! isset($vars['body']))
			{
				$vars['body'] = $body;
			}
		}

		return $this->CI->load->view('templates/' . $template_view, $vars, $return);
	}

	public function asset($asset_view, $vars = array(), $return = TRUE)
	{
		return $this->CI->load->view('assets/' . $asset_view);
	}
}