<?php

class Base extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('ppe_song_song');
	}
  
  function index()
  {
    $this->load->library('pagination');
    $page = $this->uri->segment('3', 1);
    $query = $this->ppe_song_song->getBaseEdits($page);
    $data['edits'] = $query->result();
    $config['base_url'] = 'http://' . $this->input->server('SERVER_NAME') . '/base/index/';
    $config['total_rows'] = $this->ppe_song_song->getSongCountWithGame();
    $config['per_page'] = APP_BASE_EDITS_PER_PAGE;
    $config['cur_tag_open'] = '<strong>';
    $config['cur_tag_close'] = '</strong>';
    $config['full_tag_open'] = '<p class="pager">';
    $config['full_tag_close'] = '</p>';
    $config['first_link'] = '«';
    $config['last_link'] = '»';
    $this->pagination->initialize($config);
    $this->load->view('base/main', $data);
  }
  
  // download the chosen edit to the hard drive.
  function download()
  {
    $id = $this->uri->segment(3, false);
    $st = $this->uri->segment(4, false);
    if (!(is_numeric($id) and in_array($st, array('single', 'double', 'halfdouble', 'routine'))))
    {
      # How do you cause a 409 again?
      return;
    }
    if ($this->ppe_song_song->doesSongExist($id) === 0)
    {
      $this->load->view('base/error');
      return;
    }
    $nid = sprintf("%06d", $id);
    $name = sprintf("base_%s_%s.edit", $nid, ucfirst($st));
    $gz = $name . '.gz';
    $path = sprintf("%s/data/base_edits/%s", APPPATH, $gz);
    if (!file_exists($path)) # Generate the new base edits.
    {
      $this->load->library('EditParser');
      $this->editparser->generate_base($id);
    }
    
    $file = gzopen($path, 'r');
    $data = gzread($file, APP_MAX_EDIT_FILE_SIZE);
    gzclose($file);
    
    $this->load->helper('download');
    force_download($name, $data);
  }
}