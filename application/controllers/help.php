<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Help extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
    $this->choices = array('reset', 'resend');
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');

  }
  
  function index()
  {
    $this->load->view('help/main');
  }
  
  // Ensure only reset or resend was chosen.
  function _valid_choice($str)
  {
    if (in_array($str, $this->choices)) return true;
    $this->form_validation->set_message('_valid_choice', 'A valid option was not chosen.');
    return false;
  }
  
  function check()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $this->load->view('help/missing');
      return;
    }
    // Ensure the user is actually in the system and in good standing.
    $email = $this->input->post('email');
    $this->load->model('ppe_user_user');
    $this->load->model('ppe_user_role');
    $id = $this->ppe_user_user->getIDByEmail($email);
    if (!$id)
    {
      $this->output->set_status_header(409);
      $this->load->view('help/noone');
    }
    elseif ($this->ppe_user_role->getIsUserBanned($id))
    {
      $this->output->set_status_header(409);
      $this->load->view('help/banned');
    }
    else
    {
      $username = $this->ppe_user_user->getUserByID($id);
      $this->ppe_user_user->confirmUser($id, 0);
      $this->load->model('ppe_user_condiment');
      $md5 = $this->ppe_user_condiment->updateOregano($id);
      $this->load->library('email');
      $this->load->helper('email');
      $this->email->from('jafelds@gmail.com', 'Jason "Wolfman2000" Felds');
      $this->email->to($email);
      $this->email->bcc('jafelds@gmail.com');
      if ($this->input->post('choice') === "resend")
      {
        $this->email->subject('Pump Pro Edits - Reconfirming Account');
        $this->email->message(resendMessage($md5));
      }
      else
      {
        $this->email->subject('Pump Pro Edits - Resetting Password');
        $this->email->message(resetMessage($md5));
      
      }
      $this->email->set_newline("\r\n");
      if ($this->email->send())
      {
        $this->load->view('help/sent');
      }
      else
      {
        $this->load->view('help/unsent');
      }
    }
  }
}
