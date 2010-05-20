<?php

class Usb extends Controller
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->view('usb/main');
	}
}
