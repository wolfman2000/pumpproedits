<?php

class Edits extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_user_user');
    $this->load->model('ppe_edit_edit');
    $this->load->library('pagination');
	}
	
	function index()
	{
    #$this->load->view('thanks/main');
	}
  
  /*
   * Add the common pager settings here.
   */
  function _pagerSetup($config)
  {
    $config['per_page'] = APP_MAX_EDITS_PER_PAGE;
    $config['cur_tag_open'] = '<strong>';
    $config['cur_tag_close'] = '</strong>';
    $config['full_tag_open'] = '<p class="pager">';
    $config['full_tag_close'] = '</p>';
    $config['first_link'] = '«';
    $config['last_link'] = '»';
    return $config;
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
    $query = $this->ppe_edit_edit->getEditsByUser($id, $page);
    $data['users'] = $query->result();
    
    // a lot of the code below is temporary.
    $config['base_url'] = sprintf('http://%s/user/%d/', $this->input->server('SERVER_NAME'), $id);
    $total = $this->ppe_edit_edit->getUserEditCount($id);
    $config['total_rows'] = $total;
    $data['maxEdits'] = $total;
    $this->pagination->initialize($this->_pagerSetup($config));
    
    $this->load->view('edits/user', $data);
  }
  
  // get up to (10) of a user's edits via AJAJ.
  function userConquer()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $ret = array();
    $user = $this->uri->segment(3);
    $page = $this->uri->segment(4, 1);
    $ret['edits'] = $this->ppe_edit_edit->getEditsByUser($user, $page)->result_array();
    echo json_encode($ret);
  }
  
  // get all official edits.
  function official()
  {
    $id = 2;
    $page = $this->uri->segment(2, 1);
    $query = $this->ppe_edit_edit->getEditsByUser($id, $page);
    $data['users'] = $query->result();
    
    // a lot of the code below is temporary.
    $config['base_url'] = sprintf('http://%s/official/', $this->input->server('SERVER_NAME'));
    $total = $this->ppe_edit_edit->getUserEditCount($id);
    $config['total_rows'] = $total;
    $data['maxEdits'] = $total;
    $this->pagination->initialize($this->_pagerSetup($config));
    $this->load->view('edits/official', $data);
  }
  
  // load the songs that have edits.
  function songs()
  {
    $data['query'] = $this->ppe_song_song->getSongsWithEdits()->result();
    $this->load->view('edits/songs', $data);
  }
  
  // get up to (10) of a song's edits via AJAJ.
  function songConquer()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $ret = array();
    $song = $this->uri->segment(3);
    $page = $this->uri->segment(4, 1);
    $ret['edits'] = $this->ppe_edit_edit->getEditsBySong($song, $page)->result_array();
    echo json_encode($ret);
  }
  
  // get all edits from the chosen song.
  function chosenSong()
  {
    $id = $this->uri->segment(2);
    $page = $this->uri->segment(3, 1);
    $data['song'] = $this->ppe_song_song->getSongByID($id);
    
    $query = $this->ppe_edit_edit->getEditsBySong($id, $page);
    $data['songs'] = $query->result();
    
    // a lot of the code below is temporary.
    $config['base_url'] = sprintf('http://%s/song/%d/', $this->input->server('SERVER_NAME'), $id);
    $total = $this->ppe_edit_edit->getSongEditCount($id);
    $config['total_rows'] = $total;
    $data['maxEdits'] = $total;
    $this->pagination->initialize($this->_pagerSetup($config));
    
    $this->load->view('edits/song', $data);
  }
  
  // download the chosen edit to the hard drive.
  function download()
  {
    $id = $this->uri->segment(3, false);
    // Confirm the edit isn't "deleted".
    if (!$this->ppe_edit_edit->checkExistsAndActive($id))
    {
      $this->load->helper('form');
      $this->output->set_status_header(404);
      $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('edits/deleted', $data);
      return;
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