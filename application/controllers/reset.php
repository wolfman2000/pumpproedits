<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Reset extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
		$this->_setCSS('css/reset.css');
	}
	
	function index()
	{
		$this->_setHeader('Reset your Password');
		$this->_setTitle('Reset your Password');
		$this->_loadPage(array('reset/main', 'reset/form'));
	}
	
	function check()
	{
		if ($this->form_validation->run() === FALSE)
		{
			$this->_setHeader('Password Resetting Unsuccessful');
			$this->_setTitle('Password Resetting Unsuccessful');
			$this->_loadPage(array('reset/missing', 'reset/form'));
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
			$this->_setTitle('Password Resetting Unsuccessful');
			$this->_setHeader('Password Resetting Unsuccessful');
			$this->_loadPage(array('reset/mismatch', 'reset/form'));
			//array_push($this->data, "Make sure you put in the confirmation code correctly.");
		}
		// rare to be banned while getting helped, but here it is...
		elseif ($this->ppe_user_role->getIsUserBanned($id))
		{
			$this->_setHeader('Password Reset Unsuccessful');
			$this->_setTitle('Password Reset Unsuccessful');
			$this->output->set_status_header(409);
			$this->_loadPage('reset/banned');
		}
		/* Disable this for now: something isn't right.    
		elseif ($this->ppe_user_user->getConfirmedByID($id)) // If confirmed, don't allow reseting.
		{
			$this->output->set_status_header(409);
			$this->_setHeader('No Need to Reset Password');
			$this->_setTitle('No Need to Reset Password');
			$this->session->set_flashdata('loginResult', "Log In with the same password!");
			$this->_loadPage('reset/main');
		}
		*/
		else // We're good!
		{
			$this->ppe_user_user->confirmUser($id);
			$this->ppe_user_condiment->setPassword($id, $this->input->post('password'));
			$this->session->set_userdata('id', $id);
			$this->session->set_userdata('roles', $this->ppe_user_role->getRolesByID($id));
			$this->session->set_userdata('username', $this->ppe_user_user->getUserByID($id));
			$this->_setHeader('Password Reset Successful');
			$this->_setTitle('Password Reset Successful');
			$this->_loadPage('reset/success');
		}
	}
}
