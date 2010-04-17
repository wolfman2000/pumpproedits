<?php

class Itghome extends Controller
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
    $this->load->view('itghome/main');
	}
}
