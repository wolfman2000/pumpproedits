<?php

class Register extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    $this->load->view('register/main');
  }
  
  // validation here.
  function check()
  {
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
    if ($this->form_validation->run() === FALSE)
    {
      $this->load->view('register/missing');
      return;
    }
    // Check the things the form can't do through the database.
    $this->load->model('ppe_user_user');
    $data = array();

    /* Check if the email is taken. */
    $email = $this->input->post('email');
    if ($this->ppe_user_user->getIDByEmail($email))
    {
      array_push($data['errors'], "The requested email address is already taken.");
    }
    $username = $this->input->post('username');
    $id = $this->ppe_user_user->getIDByUser($username);
    if ($id)
    {
      $this->load->model('ppe_user_role');
      // Find out WHY the username is taken. Start with banning.
      if ($this->ppe_user_role->getIsUserBanned($id))
      {
        $this->output->set_status_header(409);
        $this->load->view('register/banned');
        return;
      }
      // Not banned: see if the username is just taken.
      if ($this->ppe_user_user->getConfirmedByID($id))
      {
        array_push($data['errors'], "The requested username is already taken.");
      }
      // Not confirmed: ask for a new confirmation.
      else
      {
        array_push($data['errors'], "You need to confirm your username. See Account Help.");
      }
    }
    
    if (count($data))
    {
      $this->output->set_status_header(409);
      $this->data = $data;
      $this->load->view('register/invalid', $data);
    }
    else
    {
      // Test email sending first.
      $salt = $table->addUser($username, $email, $this->form->getValue('password'));
      $this->getMailer()->send(new RegisterConfirmationMessage($email, $username, $salt));
      $this->load->view('register/success');
    }
  }
}