<?php

class Confirm extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    $this->load->view('confirm/main');
  }
  
  // validation here.
  function check()
  {
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
    if ($this->form_validation->run() === FALSE)
    {
      $this->load->view('confirm/missing');
      return;
    }
    $this->load->model('ppe_user_condiment');
    $this->load->model('ppe_user_role');
    $oreg = $this->input->post('confirm');
    $pass = $this->input->post('password');
    $id = $this->ppe_user_condiment->confirmUser($oreg, $pass);
    
    if (!$id)
    {
      $this->output->set_status_header(409);
      $this->load->view('confirm/invalid');
    }
    elseif ($this->ppe_user_role->getIsUserBanned($id))
    {
      $this->output->set_status_header(409);
      $this->load->view('confirm/banned');
    }
    else // We're good!
    {
      $this->load->model('ppe_user_user');
      $this->ppe_user_user->confirmUser($id);
      $this->session->set_userdata('id', $id);
      $this->session->set_userdata('username', $this->ppe_user_user->getUserByID($id));
      $this->session->set_userdata('roles', $this->ppe_user_role->getRolesByID($id));
      $this->load->view('confirm/success');
    }
  }
}