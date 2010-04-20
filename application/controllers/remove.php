<?php

class Remove extends Controller
{
  function __construct()
  {
    parent::Controller();
  }
  
  function index()
  {
    if (!$this->session->userdata('id'))
    {
      redirect('login');
    }
    $this->load->view('remove/main');
  }
}