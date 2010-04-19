<?php

class Help extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    $this->load->view('help/main');
  }
  
  function check()
  {
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
    if ($this->form_validation->run() === FALSE)
    {
      $this->load->view('help/missing');
      return;
    }
  }
}