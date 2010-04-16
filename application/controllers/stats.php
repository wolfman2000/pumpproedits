<?php

class Stats extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    $this->load->view('stats/main');
  }
}