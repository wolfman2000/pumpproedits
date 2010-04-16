<?php

class Contact extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
  }
  
  function index()
  {
    $this->load->view('contact/main');
  }
  
  function mail()
  {
    if ($this->form_validation->run() === false)
    {
      $this->load->view('contact/error');
    }
    echo "so far so good!";
  }
}