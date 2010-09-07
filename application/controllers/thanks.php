<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Thanks extends Controller
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
    $this->load->view('thanks/main');
	}
}
