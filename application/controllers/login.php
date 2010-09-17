<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Login extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
	}
	
	function index()
	{
		$this->session->set_flashdata('loginResult', "Log in with the tabs above!");
		redirect('');
	}
	
	// validation here.
	function check()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
		if ($this->form_validation->run() === FALSE)
		{
			$this->session->set_flashdata('loginResult', "Fill in all fields.");
			$this->_loginRedirect();
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
		}
		elseif ($this->ppe_user_role->getIsUserBanned($id))
		{
			$this->session->unset_userdata($unset);
			$this->output->set_status_header(409);
			$this->session->set_flashdata('loginResult', "This account is banned.");
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
		}
		
		$this->_loginRedirect();
	}
}
