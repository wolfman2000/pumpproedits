<?php

class Logout extends Controller
{
  function __construct()
  {
    parent::Controller();
  }
  
  function index()
  {
    $this->session->unset_userdata(array('id' => '', 'username' => '', 'roles' => ''));
    $this->load->view('logout/main');
  }
}