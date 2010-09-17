<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Logout extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index()
	{
		$this->session->unset_userdata(array('id' => '', 'username' => '', 'roles' => ''));
		redirect((strlen($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/") , "location", 303);
	}
}
