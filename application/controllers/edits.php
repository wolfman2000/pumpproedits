<?php

class Edits extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_user_user');
    $this->load->model('ppe_edit_edit');
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
  // get all edits from the chosen user.
  function chosenUser()
  {
    $id = $this->uri->segment(2);
    $page = $this->uri->segment(3, 1);
    $data['user'] = $this->ppe_user_user->getUserByID($id);
    $data['users'] = $this->ppe_edit_edit->getEditsByUser($id)->result();
    $this->load->view('edits/user', $data);
  }
  
  // get all official edits.
  function official()
  {
    $data['users'] = $this->ppe_edit_edit->getEditsByUser(2)->result();
    $this->load->view('edits/official', $data);
  }
  
  // load the songs that have edits.
  function songs()
  {
    $data['query'] = $this->ppe_song_song->getSongsWithEdits()->result();
    $this->load->view('edits/songs', $data);
  }
  
  // get all edits from the chosen song.
  function chosenSong()
  {
    $id = $this->uri->segment(2);
    $page = $this->uri->segment(3, 1);
    $data['song'] = $this->ppe_song_song->getSongByID($id);
    $data['songs'] = $this->ppe_edit_edit->getEditsBySong($id)->result();
    $this->load->view('edits/song', $data);
  }
}