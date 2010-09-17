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
		$this->head = array
		(
			'title' => 'Pump Pro Edits',
			'css' => 'css/main.css',
			'scripts' => array('/js/jsAll.js'),
			'xhtml' => '',
		);
		$this->data = array();
		$this->foot = array();
		
	}
	
	function _setCSS($css)
	{
		$this->head['css'] = $css;
	}
	
	function _setTitle($title)
	{
		$this->head['title'] = $title . " — " . $this->head['title'];
	}
	
	function _loadPage($view)
	{
		$output  = $this->load->view('global/header', $this->head, true);
		$output .= $this->load->view($view, $this->data, true);
		$output .= $this->load->view('global/footer', $this->foot, true);
		
		$this->output->set_output($output);
	}
}
