<?php

class Base extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('itg_song_song');
	}
  
  function index()
  {
    $this->load->library('pagination');
    $page = $this->uri->segment('3', 0);
    $query = $this->itg_song_song->getBaseEdits($page);
    $data['edits'] = $query->result();
    $config['base_url'] = 'http://' . $this->input->server('SERVER_NAME') . '/base/index/';
    $config['total_rows'] = $this->itg_song_song->getSongCountWithGame();
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
    if (!(is_numeric($id) and in_array($st, array('single', 'double'))))
    {
      # How do you cause a 409 again?
      return;
    }
    if ($this->itg_song_song->doesSongExist($id) === 0)
    {
      $this->load->view('base/error');
      return;
    }
    
    $name = sprintf("base_%06d_%s.edit", $id, ucfirst($st));
    $data = $this->itg_song_song->getChosenBaseEdit((int) $id, $st);
    
    $this->load->helper('download');
    force_download($name, $data);
  }
}