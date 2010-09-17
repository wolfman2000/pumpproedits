<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Register extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->_setCSS('css/register.css');
		$this->load->helper('form');
	}
	
	function index()
	{
		$this->_setTitle('Register Here');
		$this->_setHeader('Register Here');
		$this->_loadPage('register/main');
	}
	
	// validation here.
	function check()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
		if ($this->form_validation->run() === FALSE)
		{
			$this->_setHeader('Registration Unsuccessful');
			$this->_setTitle('Registration Unsuccessful');
			$this->_loadPage('register/missing');
			return;
		}
		// Check the things the form can't do through the database.
		$this->load->model('ppe_user_user');
		$this->data['errors'] = array();
		
		/* Check if the email is taken. */
		$email = $this->input->post('email');
		if ($this->ppe_user_user->getIDByEmail($email))
		{
			array_push($this->data['errors'], "The requested email address is already taken.");
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
				$this->_setHeader('Registration Unsuccessful');
				$this->_setTitle('Registration Unsuccessful');
				$this->_loadPage('register/banned');
				return;
			}
			// Not banned: see if the username is just taken.
			if ($this->ppe_user_user->getConfirmedByID($id))
			{
				array_push($this->data['errors'], "The requested username is already taken.");
			}	
			// Not confirmed: ask for a new confirmation.
			else
			{
				array_push($this->data['errors'], "You need to confirm your username. See Account Help.");
			}
		}
		
		if (count($this->data['errors']))
		{
			$this->output->set_status_header(409);
			$this->_setHeader('Registration Unsuccessful');
			$this->_setTitle('Registration Unsuccessful');
			$this->_loadPage('register/invalid');
		}
		else
		{
			// Test email sending first.
			$md5 = $this->ppe_user_user->addUser($username, $email, $this->input->post('password'));
			$this->load->library('email');
			$this->load->helper('email');
			$this->email->from('jafelds@gmail.com', 'Jason "Wolfman2000" Felds');
			$this->email->to($email);
			$this->email->bcc('jafelds@gmail.com');
			$this->email->subject('Pump Pro Edits - Registration Confirmation');
			$this->email->message(registerMessage($md5));
			$this->email->set_newline("\r\n");
			if ($this->email->send())
			{
				$this->_setHeader('Almost Registered!');
				$this->_setTitle('Almost Registered!');
				$this->_loadPage('register/success');
			}
			else
			{
				$this->_setHeader('Registration Email not sent!');
				$this->_setTitle('Registration Email not sent!');
				$this->_loadPage('register/unsent');
			}
		}
	}
}
