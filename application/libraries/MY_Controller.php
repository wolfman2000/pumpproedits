<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Wolf_Controller extends Controller
{
	function __construct()
	{
		parent::Controller();
		$browser = browser_detection('browser_working');
		$modern = ($browser === "ie" ? browser_detection('ie_version') == "ie9x" : true);
		$this->data = array
		(
			'title' => 'Pump Pro Edits',
			'css' => 'css/main.css',
			'scripts' => array('/js/jsAll.js'),
			'xhtml' => '',
			'browser' => $browser,
			'modern' => $modern,
			'h2' => "Welcome!",
		);
	}
	
	function _setCSS($css)
	{
		$this->data['css'] = $css;
	}
	
	function _setTitle($title)
	{
		$this->data['title'] = $title . " — " . $this->data['title'];
	}
	
	function _loadPage($view)
	{
		$output  = $this->load->view('global/header', $this->data, true);
		$output .= $this->load->view('global/nav_normal', $this->data, true);
		$output .= $this->load->view($view, $this->data, true);
		$output .= $this->load->view('global/footer', $this->data, true);
		
		$this->output->set_output($output);
	}
}
