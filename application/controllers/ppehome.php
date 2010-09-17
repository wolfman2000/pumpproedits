<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Ppehome extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->load->model('ppe_edit_edit');
		$this->data['query'] = $this->ppe_edit_edit->getEditsEntry()->result();
		$this->data['showuser'] = 1;
		$this->data['showsong'] = 1;
		$this->_loadPage('ppehome/main');
	}
}
