<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Login extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    $this->load->view('login/main');
  }
  
  // validation here.
  function check()
  {
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
    if ($this->form_validation->run() === FALSE)
    {
      $this->session->set_flashdata('loginResult', "Fill in all fields.");
      redirect((strlen($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/"), "location", 303);
      #$this->load->view('login/missing');
      return;
    }
    $this->load->model('ppe_user_condiment');
    $this->load->model('ppe_user_role');
    $user = $this->input->post('username');
    $pass = $this->input->post('password');
    
    $id = $this->ppe_user_condiment->checkUser($user, $pass);
    
    $unset = array('id' => '', 'username' => '', 'roles' => '');
    
    if (!$id)
    {
      $this->session->unset_userdata($unset);
      $this->output->set_status_header(409);
      $this->session->set_flashdata('loginResult', "Invalid username/password combination.");
      # $this->load->view('login/invalid');
    }
    elseif ($this->ppe_user_role->getIsUserBanned($id))
    {
      $this->session->unset_userdata($unset);
      $this->output->set_status_header(409);
      $this->session->set_flashdata('loginResult', "This account is banned.");
      #$this->load->view('login/banned');
    }
    else
    {
      $roles = $this->ppe_user_role->getRolesByID($id);
      $this->load->model('ppe_user_user');
      $this->session->set_userdata('id', $id);
      $name = $this->ppe_user_user->getCasedName($user);
      $this->session->set_userdata('username', $name);
      $this->session->set_userdata('roles', $roles);
      $this->session->set_flashdata('loginResult', "Welcome $name.");
      #$this->load->view('login/success');
    }
    
    redirect((strlen($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "/"), "location", 303);
  }
}
