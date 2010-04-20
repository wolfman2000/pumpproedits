<?php

class Create extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->helper('form');
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_song_game');
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
  
  
  
  // Give the user help upon request.
  function help()
  {
    $this->load->view('create/help');
  }
  
  // Determine if the chosen song can have routine charts.
  function routine()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $sid = $this->uri->segment(3);
    $ret['isRoutine'] = $this->ppe_song_game->getRoutineCompatible($sid);
    echo json_encode($ret);
  }
  
  // Download the edit created directly.
  function download()
  {
  
  }
}