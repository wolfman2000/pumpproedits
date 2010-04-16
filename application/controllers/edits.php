<?php

class Edits extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_user_user');
	}
	
	function index()
	{
    #$this->load->view('thanks/main');
	}
  
  function users()
  {
    $data['query'] = $this->ppe_user_user->getUsersWithEdits()->result();
    $this->load->view('edits/users', $data);
  }
  
  // load the songs that have edits.
  function songs()
  {
    $data['query'] = $this->ppe_song_song->getSongsWithEdits()->result();
    $this->load->view('edits/songs', $data);
  }
}