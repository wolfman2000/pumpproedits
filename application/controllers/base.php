<?php

class Base extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_edit_edit');
	}
  
  function index()
  {
    $this->load->library('pagination');
    $query = $this->ppe_song_song->getBaseEdits();
    $data['edits'] = $query->result();
    $config['base_url'] = 'http://' . $this->input->server('server_name') . 'base/';
    $config['total_rows'] = $query->num_rows();
    $config['per_page'] = APP_BASE_EDITS_PER_PAGE;
    $config['first_link'] = "«";
    $config['last_link'] = "»";
    $this->pagination->initialize();
    $page = $this->uri->segment('3', 1);
    $data['page'] = $this->pagination->create_links();
    $this->load->view('base/main', $data);
  }
}