<?php

class Chart extends Controller
{
  function __construct()
  {
    parent::Controller();
    $this->load->library('form_validation');
    $this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
    $this->load->model('ppe_edit_edit');
    $this->load->model('ppe_song_song');
    $this->difficulties = array('ez', 'nr', 'hr', 'cz', 'hd', 'fs', 'nm', 'rt');
  }
  
  function index()
  {
    redirect('chart/edits');
  }
  
  function edits()
  {
    $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
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
    if ($this->ppe_edit_edit->checkExistance($str)) return true;
    $this->form_validation->set_message('_edit_exists', "The edit chosen $str doesn't have a corresponding file.");
    return false;
  }
  
  // confirm the note color exists.
  function _notecolor_exists($str)
  {
    if (in_array($str, array('classic', 'rhythm'))) return true;
    $this->form_validation->set_message('_notecolor_exists', "Please choose either the classic or rhythm color setup.");
    return false;
  }
  
  // confirm the note skin exists.
  function _noteskin_exists($str)
  {
  	if (in_array($str, array('original', 'stepmania'))) return true;
  	$this->form_validation->set_message('_noteskin_exists', "Please choose either the original or stepmania noteskin.");
  	return false;
  }
  
  // confirm the 4th note color is valid.
  function _red_exists($str)
  {
    if (in_array($str, array(0, 1))) return true;
    $this->form_validation->set_message('_red_exists', "Decide the color of the rhythm quarter notes.");
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
    return in_array($str, array(4, 6, 8, 12, 16));
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
      $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/editError', $data);
      return;
    }
    $eid = $this->input->post('edits');
    
    // Confirm the edit isn't "deleted".
    if (!$this->ppe_edit_edit->checkExistsAndActive($eid))
    {
      $this->output->set_status_header(404);
      $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/deleted', $data);
      return;
    }
    
    $path = sprintf("%sdata/user_edits/edit_%06d.edit.gz", APPPATH, $eid);
    if (!file_exists($path))
    {
      $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/editError', $data);
      return;
    }
    $this->load->model('ppe_user_user');
    $author = $this->ppe_user_user->getUserByEditID($eid);
    $this->load->library('EditParser');
    $p = array('notes' => 1, 'strict_song' => 0, 'strict_edit' => 0, 'author' => $author);
    $notedata = $this->editparser->get_stats(gzopen($path, "r"), $p);
    $p = array('cols' => $notedata['cols'], 'kind' => $this->input->post('kind'),
      'red4' => $this->input->post('red4'), 'speed_mod' => $this->input->post('speed'),
      'mpcol' => $this->input->post('mpcol'), 'scale' => $this->input->post('scale'),
      'author' => $author);
    $this->load->library('EditCharter', $p);
    $notedata['author'] = $author;
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    echo $xml->saveXML();
  }
  
  // get the list of songs for possible chart previewing.
  function songs()
  {
    $data['songs'] = $this->ppe_song_song->getSongsWithGameAndDiff()->result_array();
    $this->load->view('chart/songs', $data);
  }
  
  // Use AJAJ to get the difficulties charted for each song.
  function diff()
  {
    $sid = $this->uri->segment(3, false);
    header("Content-type: application/json");
    $path = "%sdata/official/%d_%s.sm.gz";
    foreach ($this->difficulties as $d)
    {
      $ret[$d] = file_exists(sprintf($path, APPPATH, $sid, $d));
    }
    echo json_encode($ret);
  }
  
  function songProcess()
  {
    if ($this->form_validation->run() === FALSE)
    {
      $data['songs'] = $this->ppe_song_song->getSongsWithGameAndDiff()->result_array();
      $this->load->view('chart/songError', $data);
      return;
    }
    $sid = $this->input->post('songs');
    $dif = $this->input->post('diff');
    $path = sprintf("%sdata/official/%d_%s.sm.gz", APPPATH, $sid, $dif);
    
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
    
    // Confirm the edit isn't "deleted".
    if (!$this->ppe_edit_edit->checkExistsAndActive($id))
    {
      $this->output->set_status_header(404);
      $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/deleted', $data);
      return;
    }
    
    $kind = $this->uri->segment(4, FALSE);
    if (!(is_numeric($id) and ($kind === "classic" or $kind === "rhythm")))
    {
      # Return error here: parameters must match.
    }
    $this->load->model('ppe_user_user');
    $user = $this->ppe_user_user->getUserByEditID($id);
    $id = sprintf("%06d", $id);
    $name = sprintf("edit_%s.edit.gz", $id);
    $path = sprintf("%sdata/user_edits/%s", APPPATH, $name);
    
    if (!file_exists($path))
    {
      $data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
      $this->load->view('chart/none', $data);
      return;
    }
    // Validate the file and print the chart here.
    $this->load->library('EditParser');
    $notedata = $this->editparser->get_stats(gzopen($path, "r"),
      array('notes' => 1, 'strict_edit' => 0));
    $notedata['author'] = $user;
    $p = array('cols' => $notedata['cols'], 'kind' => $kind);
    $this->load->library('EditCharter', $p);
    header("Content-Type: application/xhtml+xml");
    $xml = $this->editcharter->genChart($notedata);
    
    echo $xml->saveXML();
  }
}
