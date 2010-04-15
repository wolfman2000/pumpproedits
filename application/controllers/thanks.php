<?php

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