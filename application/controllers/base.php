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
  
  // download the chosen edit to the hard drive.
  function download()
  {
    $id = $this->uri->segment(3, false);
    $st = $this->uri->segment(4, false);
    if (!(is_numeric($id) and in_array($st, array('single', 'double', 'halfdouble', 'routine'))))
    {
      # How do you cause a 409 again?
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