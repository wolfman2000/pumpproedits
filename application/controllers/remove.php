<?php

class Remove extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->helper('form');
    $this->load->model('ppe_edit_edit');
  }
  
  function index()
  {
    $id = $this->session->userdata('id');
    if (!$id)
    {
      redirect('login');
    }
    $data['edits'] = $this->ppe_edit_edit->getEditsToDelete($id)->result_array();
    $this->load->view('remove/main', $data);
  }
}