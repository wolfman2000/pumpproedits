<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Thanks extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();	
	}
	
	function index()
	{
		$this->_setTitle('Credits / Thanks');
		$this->_setCSS('css/thanks.css');
		$this->_setHeader('Credits and Thanks');
		$this->_loadPage('thanks/main');
	}
}
