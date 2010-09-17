<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Confirm extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->_setCSS('css/confirm.css');
	}
	
	function index()
	{
		$this->_setHeader('Confirm your Account');
		$this->_setTitle('Confirm your Account');
		$this->_loadPage(array('confirm/main', 'confirm/form'));
	}
	
	// validation here.
	function check()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
		$this->_setHeader('Confirmation Unsuccessful');
		$this->_setTitle('Confirmation Unsuccessful');
		if ($this->form_validation->run() === FALSE)
		{
			$this->_loadPage(array('confirm/missing', 'confirm/form'));
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
			$this->_loadPage(array('confirm/invalid', 'confirm/form'));
		}
		elseif ($this->ppe_user_role->getIsUserBanned($id))
		{
			$this->output->set_status_header(409);
			$this->_loadPage('confirm/banned');
		}
		else // We're good!
		{
			$this->load->model('ppe_user_user');
			$this->ppe_user_user->confirmUser($id);
			$this->session->set_userdata('id', $id);
			$this->session->set_userdata('username', $this->ppe_user_user->getUserByID($id));
			$this->session->set_userdata('roles', $this->ppe_user_role->getRolesByID($id));
			$this->_setHeader('Confirmation Successful');
			$this->_setTitle('Confirmation Successful');
			$this->_loadPage('confirm/success');
		}
	}
}
