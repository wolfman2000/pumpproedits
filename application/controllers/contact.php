<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Contact extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
		$this->_setCSS('css/contact.css');
	}
	
	function index()
	{
		$this->_setHeader('Contact the Webmaster');
		$this->_setTitle('Contact the Webmaster');
		$this->_loadPage('contact/main');
	}
  
	function mail()
	{
		if ($this->form_validation->run() === false)
		{
			$this->_setHeader('Webmaster Contact Error');
			$this->_setTitle('Webmaster Contact Error');
			$this->_loadPage('contact/error');
			return;
		}
		$this->load->library('email');
		$this->email->from('jafelds@gmail.com', 'Jason "Wolfman2000" Felds');
		$this->email->to('jafelds@gmail.com');
		$this->email->bcc('jafelds@gmail.com');
		$this->email->reply_to($this->input->post('email'), $this->input->post('name'));
		$this->email->subject('PPEdits Contact Form - ' . $this->input->post('subject'));
		$this->email->message($this->input->post('content'));
		$this->email->set_newline("\r\n");
		if ($this->email->send())
		{
			$this->_setHeader('Webmaster Contact Success');
			$this->_setTitle('Webmaster Contact Success');
			$this->_loadPage('contact/sent');
		}
		else
		{
			$this->_setHeader('Webmaster Contact Error');
			$this->_setTitle('Webmaster Contact Error');
			$this->_loadPage('contact/unsent');
		}
	}
}
