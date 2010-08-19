<?php

class Ppehome extends Controller
{
	function __construct()
	{
		parent::Controller();	
	}
	
	function index()
	{
		$this->load->model('ppe_edit_edit');
		$data['query'] = $this->ppe_edit_edit->getEditsEntry()->result();
		$this->load->view('ppehome/main', $data);
	}
}
