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
      $time = date('YmdHis');
      $full = $data['upload_data']['full_path'];
      $dest = $data['upload_data']['file_path'] . $time . '.edit';
      //move_uploaded_file($full, $dest);
      $this->load->library('EditParser');
      try
      {
        $data['result'] = $this->editparser->get_stats(gzopen($full, "r"));
        @unlink($full);
        $this->load->view('stats/stats', $data);
      }
      catch (Exception $e)
      {
        @unlink($full);
        $this->load->view('stats/invalid', array('error' => $e));
      }
    }
    else
    {
      $this->load->view('stats/error');
    }
  }
}