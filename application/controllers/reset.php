<?php

class Reset extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');

  }
  
  function index()
  {
    $this->load->view('reset/main');
  }
  
  function check()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $this->load->view('reset/missing');
      return;
    }
    $oreg = $this->input->post('confirm');
    $this->load->model('ppe_user_condiment');
    $this->load->model('ppe_user_role');
    $this->load->model('ppe_user_user');
    $id = $this->ppe_user_condiment->getIDByOregano($oreg);

    if (!$id)
    {
      $this->output->set_status_header(409);
      $this->load->view('reset/mismatch');
      //array_push($this->data, "Make sure you put in the confirmation code correctly.");
    }
    // rare to be banned while getting helped, but here it is...
    elseif ($this->ppe_user_role->getIsUserBanned($id))
    {
      $this->output->set_status_header(409);
      $this->load->view('reset/banned');
    }
    /* Disable this for now: something isn't right.    
    elseif ($this->ppe_user_user->getConfirmedByID($id)) // If confirmed, don't allow reseting.
    {
      $this->output->set_status_header(409);
      $this->load->view('reset/noneed');
    }
    */
    else // We're good!
    {
      $this->ppe_user_user->confirmUser($id);
      $this->ppe_user_condiment->setPassword($id, $this->input->post('password'));
      $this->session->set_userdata('id', $id);
      $this->session->set_userdata('roles', $this->ppe_user_role->getRolesByID($id));
      $this->session->set_userdata('username', $this->ppe_user_user->getUserByID($id));
      $this->load->view('reset/success');
    }
  }
}
