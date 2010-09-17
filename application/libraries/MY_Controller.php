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
	}
	
	function _loadPage($view, $data)
	{
		$output  = $this->load->view('global/header', $data, true);
		$output .= $this->load->view($view, $data, true);
		$output .= $this->load->view('global/footer', $data, true);
		
		$this->output->set_output($output);
	}
}
