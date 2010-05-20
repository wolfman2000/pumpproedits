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
      $data = file_get_contents($full);
      
      try
      {
        $row = $this->editparser->get_stats(gzopen($full, "r"));
        @unlink($full);
      }
      catch (Exception $e)
      {
        @unlink($full);
        $this->load->view('upload/invalid', array('error' => $e));
        return;
      }
      $this->load->model('ppe_edit_edit');
      $uid = $this->input->post('userid');
      $row['uid'] = $uid;
      $this->load->model('ppe_song_song');
      $song = $this->ppe_song_song->getSongByID($row['id']);
      $eid = $this->ppe_edit_edit->getIDByUpload($row);
      // if old edit: update/replace
      if ($eid)
      {
        $status = "updated";
        $this->ppe_edit_edit->updateEdit($eid, $row);
      }
      else
      {
        $eid = $this->ppe_edit_edit->addEdit($row);
        $status = "created";
      }
      $this->db->cache_delete_all();
      $this->load->helper('twitter');
      $this->load->model('ppe_user_user');
      $twit = genEditMessage($uid, $this->ppe_user_user->getUserByID($uid), $status,
        $row['style'], $row['title'], $song);
      postTwitter($twit);
      
      $path = sprintf("%sdata/user_edits/edit_%06d.edit.gz", APPPATH, $eid);
      $fp = gzopen($path, "w");
      gzwrite($fp, $data);
      gzclose($fp);
      $this->load->view('upload/success');
    }
    else
    {
      $this->load->view('upload/error');
    }
  }
}
