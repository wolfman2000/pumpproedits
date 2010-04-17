<?php

class Edits extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('itg_song_song');
    $this->load->model('itg_user_user');
    $this->load->model('itg_edit_edit');
	}
	
	function index()
	{
    #$this->load->view('thanks/main');
	}
  
  function users()
  {
    $data['query'] = $this->itg_user_user->getUsersWithEdits()->result();
    $this->load->view('edits/users', $data);
  }
  // get all edits from the chosen user.
  function chosenUser()
  {
    $id = $this->uri->segment(2);
    $page = $this->uri->segment(3, 1);
    $data['user'] = $this->itg_user_user->getUserByID($id);
    $data['users'] = $this->itg_edit_edit->getEditsByUser($id)->result();
    $this->load->view('edits/user', $data);
  }
  
  // load the songs that have edits.
  function songs()
  {
    $data['query'] = $this->itg_song_song->getSongsWithEdits()->result();
    $this->load->view('edits/songs', $data);
  }
  
  // get all edits from the chosen song.
  function chosenSong()
  {
    $id = $this->uri->segment(2);
    $page = $this->uri->segment(3, 1);
    $data['song'] = $this->itg_song_song->getSongByID($id);
    $data['songs'] = $this->itg_edit_edit->getEditsBySong($id)->result();
    $this->load->view('edits/song', $data);
  }
  
  // download the chosen edit to the hard drive.
  function download()
  {
    $id = $this->uri->segment(3, false);
    if (!(is_numeric($id)))
    {
      # How do you cause a 409 again?
    }
    $id = sprintf("%06d", $id);
    $name = sprintf("edit_%s.edit", $id);
    $gz = $name . '.gz';
    $path = sprintf("%s/data/user_edits/%s", APPPATH, $gz);
    $file = gzopen($path, 'r');
    $data = gzread($file, APP_MAX_EDIT_FILE_SIZE);
    gzclose($file);
    
    $this->load->helper('download');
    force_download($name, $data);
  }
}