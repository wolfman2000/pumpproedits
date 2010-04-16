<?php

class Stats extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    $this->load->view('stats/main');
  }
  
  function process()
  {
    $config['upload_path'] = realpath(BASEPATH.'../tmp/');
		$config['allowed_types'] = 'edit';
		$config['max_size']	= '60';
		
    $this->load->library('upload', $config);
    
    if ($this->upload->do_upload("file"))
    {
      $data = array('upload_data' => $this->upload->data());
			$this->load->view('stats/stats', $data);
    }
    else
    {
      echo $this->upload->display_errors();
      echo "The file did not get uploaded.";
    }
  }
}