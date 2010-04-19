<?php

class Upload extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->helper('form');
  }
  
  function index()
  {
    if (!$this->session->userdata('id'))
    {
      $this->output->set_status_header(409);
      $this->load->view('upload/auth');
    }
    else
    {
      $this->load->view('upload/main');
    }
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
        $this->load->view('upload/success', $data);
      }
      catch (Exception $e)
      {
        @unlink($full);
        $this->load->view('upload/invalid', array('error' => $e));
      }
    }
    else
    {
      $this->load->view('upload/error');
    }
  }
}