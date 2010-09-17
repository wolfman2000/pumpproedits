<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Usb extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->_setCSS('css/usb.css');
		$this->_setTitle('Guide to USB functionality in the Pump it up Pro series');
		$this->_setHeader('Guide to USB functionality in the Pump it up Pro series');
	}
	
	function index()
	{
		$this->_loadPage('usb/main');
	}
}
