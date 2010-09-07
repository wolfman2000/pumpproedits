<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Ppehome extends Controller
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->model('ppe_edit_edit');
		$data['query'] = $this->ppe_edit_edit->getEditsEntry()->result();
		$data['showuser'] = 1;
		$data['showsong'] = 1;
		$this->load->view('ppehome/main', $data);
	}
}
