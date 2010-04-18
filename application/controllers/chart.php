<?php

class Chart extends Controller
{
	function __construct()
	{
    $this->difficulties = array('sb', 'se', 'sm', 'sh', 'sx', 'de', 'dm', 'dh', 'dx');
		parent::Controller();
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
    $this->load->model('itg_edit_edit');
    $this->load->model('itg_song_song');
  }
  
  function index()
  {
    redirect('chart/edits');
  }
  
  function edits()
  {
    $data['edits'] = $this->itg_edit_edit->getNonProblemEdits()->result_array();
    $this->load->view('chart/edits', $data);
  }
  
  // confirm the song and difficulty exist.
  function _diff_exists($str)
  {
    if (in_array($str, $this->difficulties)) return true;
    $this->form_validation->set_message('_diff_exists', 'A valid difficulty must be chosen.');
    return false;
  }
  
  // confirm the edit exists.
  function _edit_exists($str)
  {
    if ($this->itg_edit_edit->checkExistance($str)) return true;
    $this->form_validation->set_message('_edit_exists', "The edit chosen $str doesn't have a corresponding file.");
    return false;
  }
  
  // confirm the speed mod is valid.
  function _speed_valid($str)
  {
    if (in_array($str, array(1, 2, 3, 4, 6, 8))) return true;
    $this->form_validation->set_message('_speed_valid', 'A valid speed mod must be chosen.');
    return false;
  }
  
  // confirm the number of measures in each column is valid.
  function _mpc_valid($str)
  {
    if (in_array($str, array(4, 6, 8, 12, 16))) return true;
    $this->form_validation->set_message('_mpc_valid', 'An unknown number of measures per column was chosen.');
    return false;
  }
  
  // confirm the scale factor is valid.
  function _scale_valid($str)
  {
    if (in_array($str, array(0.5, 0.75, 1, 1.25, 1.5, 1.75, 2))) return true;
    $this->form_validation->set_message('_edit_exists', 'The scale chosen was not a valid scale.');
    return false;
  }
  
  function editProcess()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $data['edits'] = $this->itg_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/editError', $data);
      return;
    }
    $eid = $this->input->post('edits');
    $path = sprintf("%sdata/itg_user_edits/itg_%06d.edit.gz", APPPATH, $eid);
    if (!file_exists($path))
    {
      $data['edits'] = $this->itg_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/editError', $data);
      return;
    }
    $this->load->model('itg_user_user');
    $author = $this->itg_user_user->getUserByOldEditID($eid);
    $this->load->library('EditParser');
    $p = array('notes' => 1, 'strict_song' => 0, 'strict_edit' => 0);
    $notedata = $this->editparser->get_stats(gzopen($path, "r"), $p);
    $p = array('cols' => $notedata['cols'], 'kind' => $this->input->post('kind'),
      'red4' => $this->input->post('red4'), 'speed_mod' => $this->input->post('speed'),
      'mpcol' => $this->input->post('mpcol'), 'scale' => $this->input->post('scale'));
    $this->load->library('EditCharter', $p);
    $notedata['author'] = $author;
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    echo $xml->saveXML();
  }
  
  // get the list of songs for possible chart previewing.
  function songs()
  {
    $data['songs'] = $this->itg_song_song->getSongsWithGameAndDiff()->result_array();
    $this->load->view('chart/songs', $data);
  }
  
  // Use AJAJ to get the difficulties charted for each song.
  function diff()
  {
    $sid = $this->uri->segment(3, false);
    header("Content-type: application/json");
    $path = "%sdata/itg_official/%d_%s.sm.gz";//, APPPATH, $sid);
    foreach ($this->difficulties as $d)
    {
      $ret[$d] = file_exists(sprintf($path, APPPATH, $sid, $d));
    }
    //$ret = $this->itg_song_song->getDifficulties($sid);
    echo json_encode($ret);
  }
  
  function songProcess()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $data['songs'] = $this->itg_song_song->getSongsWithGameAndDiff()->result_array();
      $this->load->view('chart/songError', $data);
      return;
    }
    $sid = $this->input->post('songs');
    $dif = $this->input->post('diff');
    $path = sprintf("%sdata/itg_official/%d_%s.sm.gz", APPPATH, $sid, $dif);
    
    $this->load->library('EditParser');
    $arc = $this->editparser->getStyle(substr($dif, 1, 1));
    $st = substr($dif, 0, 1) === "s" ? "Single" : "Double";
    $p = array('notes' => 1, 'strict_song' => 0, 'arcade' => $arc,
      'style' => $st);
    $notedata = $this->editparser->get_stats(gzopen($path, "r"), $p);
    $p = array('cols' => $notedata['cols'], 'kind' => $this->input->post('kind'),
      'red4' => $this->input->post('red4'), 'speed_mod' => $this->input->post('speed'),
      'mpcol' => $this->input->post('mpcol'), 'scale' => $this->input->post('scale'), 'arcade' => 1);
    $this->load->library('EditCharter', $p);
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    echo $xml->saveXML();
  }
  
  function quick()
  {
    $id = $this->uri->segment(3, FALSE);
    if (!is_numeric($id))
    {
      # Return error here: parameters must match.
    }
    $id = sprintf("%06d", $id);
    $name = sprintf("itg_%s.edit.gz", $id);
    $path = sprintf("%s/data/itg_user_edits/%s", APPPATH, $name);
    
    if (!file_exists($path))
    {
      # Return error: file must exist.
    }
    // Validate the file and print the chart here.
    $this->load->library('EditParser');
    $notedata = $this->editparser->get_stats(gzopen($path, "r"),
      array('notes' => 1, 'strict_edit' => 0));
    $p = array('cols' => $notedata['cols']);
    $this->load->library('EditCharter', $p);
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    
    echo $xml->saveXML();
  }
}