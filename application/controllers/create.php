<?php

class Create extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->helper('form');
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_user_power');
  }
  
  // load the main page...unless stuck on IE.
  function index()
  {
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false)
    {
      $this->output->set_status_header(415);
      $this->load->view('create/ie');
      return;
    }
    $data = array();
    $data['songs'] = $this->ppe_song_song->getSongsWithGame();
    $id = $this->session->userdata('id');
    if (!$id)
    {
      $data['andy'] = 0;
      $data['others'] = 0;
    }
    else
    {
      $data['andy'] = $this->ppe_user_power->canEditOfficial($id);
      $data['others'] = $this->ppe_user_power->canEditOthers($id);
    }
    header("Content-Type: application/xhtml+xml");
    $this->load->view('create/main', $data);
  }
  
  
  
  // Download the edit created directly.
  function download()
  {
  
  }
}