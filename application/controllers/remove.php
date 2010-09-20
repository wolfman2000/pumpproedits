<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Remove extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->helper('form');
		$this->load->model('ppe_edit_edit');
		$this->_setCSS('css/remove.css');
	}
	
	function index()
	{
		$id = $this->session->userdata('id');
		if (!$id)
		{
			redirect('login');
		}
		$this->data['edits'] = $this->ppe_edit_edit->getEditsToDelete($id)->result_array();
		$this->_setTitle('Remove your Edits');
		$this->_setHeader('Remove your Edits');
		$this->_loadPage(array('remove/main', 'remove/form'));
	}
	
	function process()
	{
		$id = $this->session->userdata('id');
		if (!$id)
		{
			redirect('login');
		}
		$this->ppe_edit_edit->removeEdits($this->input->post('removing'));
		$this->data['edits'] = $this->ppe_edit_edit->getEditsToDelete($id)->result_array();
		$this->_setTitle('Selected Edits Removed');
		$this->_setHeader('Selected Edits Removed');
		$this->_loadPage(array('remove/deleted', 'remove/form'));
	}
}
