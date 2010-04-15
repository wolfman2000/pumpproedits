<?php

class Ppehome extends Controller
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
    $this->load->view('ppehome/main');
	}
}
