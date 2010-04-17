<?php

class Chart extends Controller
{
	function __construct()
	{
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
    $data['edits'] = $this->itg_edit_edit->getNonProblemEdits()->result();
    $this->load->view('chart/edits', $data);
  }
  
  // confirm the song and difficulty exist.
  function _diff_exists($str)
  {
    if (in_array($str, array('sb', 'se', 'sm', 'sh', 'sx', 'de', 'dm', 'dh', 'dx'))) return true;
    return false;
  }
  
  // confirm the edit exists.
  function _edit_exists($str)
  {
    return $this->itg_edit_edit->checkExistance($str);
  }
  
  // confirm the noteskin exists.
  function _noteskin_exists($str)
  {
    return in_array($str, array('classic', 'rhythm'));
  }
  
  // confirm the 4th note color is valid.
  function _red_exists($str)
  {
    return in_array($str, array(0, 1));
  }
  
  // confirm the speed mod is valid.
  function _speed_valid($str)
  {
    return in_array($str, array(1, 2, 3, 4, 6, 8));
  }
  
  // confirm the number of measures in each column is valid.
  function _mpc_valid($str)
  {
    return in_array($str, array(4, 6, 8, 12, 16));
  }
  
  // confirm the scale factor is valid.
  function _scale_valid($str)
  {
    return in_array($str, array(0.5, 0.75, 1, 1.25, 1.5, 1.75, 2));
  }
  
  function editProcess()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $data['edits'] = $this->itg_edit_edit->getNonProblemEdits()->result();
      $this->load->view('chart/editError', $data);
      return;
    }
    $eid = $this->input->post('edits');
    $path = sprintf("%sdata/user_edits/edit_%06d.edit.gz", APPPATH, $eid);
    $this->load->model('itg_user_user');
    $author = $this->itg_user_user->getUserByEditID($eid);
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
    $data['songs'] = $this->itg_song_song->getSongsWithGameAndDiff()->result();
    $this->load->view('chart/songs', $data);
  }
  
  // Use AJAJ to get the difficulties charted for each song.
  function diff()
  {
    $sid = $this->uri->segment(3, false);
    header("Content-type: application/json");
    $ret = $this->itg_song_song->getDifficulties($sid);
    echo json_encode($ret);
  }
  
  function songProcess()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $data['songs'] = $this->itg_song_song->getSongsWithGameAndDiff()->result();
      $this->load->view('chart/songError', $data);
      return;
    }
    $sid = $this->input->post('songs');
    $dif = $this->input->post('diff');
    $path = sprintf("%sdata/itg_official/%d.sm", APPPATH, $sid);
    
    $this->load->library('EditParser');
    $p = array('notes' => 1, 'strict_song' => 0, 'arcade' => $dif);
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
    $kind = $this->uri->segment(4, FALSE);
    if (!(is_numeric($id) and ($kind === "classic" or $kind === "rhythm")))
    {
      # Return error here: parameters must match.
    }
    $id = sprintf("%06d", $id);
    $name = sprintf("edit_%s.edit.gz", $id);
    $path = sprintf("%s/data/user_edits/%s", APPPATH, $name);
    
    if (!file_exists($path))
    {
      # Return error: file must exist.
    }
    // Validate the file and print the chart here.
    $this->load->library('EditParser');
    $notedata = $this->editparser->get_stats(gzopen($path, "r"),
      array('notes' => 1, 'strict_edit' => 0));
    $p = array('cols' => $notedata['cols'], 'kind' => $kind);
    $this->load->library('EditCharter', $p);
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    
    echo $xml->saveXML();
  }
}