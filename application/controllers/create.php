<?php

class Create extends Controller
{
	function __construct()
	{
		parent::Controller();
    $this->load->helper('form');
    $this->load->model('ppe_song_song');
    $this->load->model('ppe_song_game');
    $this->load->model('ppe_user_power');
    $this->load->model('ppe_user_user');
    $this->load->model('ppe_song_bpm');
    $this->load->model('ppe_song_stop');
    $this->load->model('ppe_edit_edit');
    $this->load->library('EditParser');
    $this->songs = $this->ppe_song_song->getSongsWithGame();
  }
  
  // load the main page...unless stuck on IE.
  function index()
  {
    if (strpos($_SERVER['HTTP_USER_AGENT'], "MSIE") !== false)
    {
      $this->output->set_status_header(415);
      $this->load->view('create/ie');
      return;
    }
    $data = array();
    $data['songs'] = $this->songs;
    $id = $this->session->userdata('id');
    if (!$id)
    {
      $data['andy'] = 0;
      $data['others'] = 0;
    }
    else
    {
      $data['andy'] = $this->ppe_user_power->canEditOfficial($id);
      $data['others'] = $this->ppe_user_power->canEditOthers($id);
    }
    header("Content-Type: application/xhtml+xml");
    $this->load->view('create/main', $data);
  }
  
  // Load the edit from the hard drive...via textarea.
  function loadTextarea()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $ret = array();
    $file = $this->input->post('file');
    
    $fp = null;
    $time = date('YmdHis');
    $fn = sprintf("%s%s.edit.gz", APPPATH, $time);
    
    try
    {
      $fp = gzopen($fn, "w");
      gzwrite($fp, $file);
      gzclose($fp);
      
      $this->load->library('EditParser');
      
      $st = $this->editparser->get_stats(gzopen($fn, "r"), array('notes' => 1));
      $ret['id'] = $st['id'];
      $ret['diff'] = $st['diff'];
      $ret['style'] = substr($st['style'], 5);
      $ret['title'] = $st['title'];
      $ret['steps'] = $st['steps'];
      $ret['jumps'] = $st['jumps'];
      $ret['holds'] = $st['holds'];
      $ret['mines'] = $st['mines'];
      $ret['trips'] = $st['trips'];
      $ret['rolls'] = $st['rolls'];
      $ret['lifts'] = $st['lifts'];
      $ret['fakes'] = $st['fakes'];
      $ret['notes'][0] = $st['notes'][0];
      if ($ret['style'] === "routine" or $ret['style'] === "pump-routine")
      {
        $ret['notes'][1] = $st['notes'][1];
      }
    }
    catch (Exception $e)
    {
      $ret['exception'] = $e->getMessage();
    }
    @unlink($fn);
    echo json_encode($ret);
  }
  
  // Give the user help upon request.
  function help()
  {
    $this->load->view('create/help');
  }
  
  // Determine if the chosen song can have routine charts.
  function routine()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $sid = $this->uri->segment(3);
    $ret['isRoutine'] = $this->ppe_song_game->getRoutineCompatible($sid);
    echo json_encode($ret);
  }
  
  // Load measure/sync data for the chosen song.
  function song()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $sid = $this->uri->segment(3);
    $row = $this->ppe_song_song->getCreatorData($sid);
    $ret['name'] = $row->name;
    $ret['abbr'] = $row->abbr;
    $ret['measures'] = $row->measures;
    $ret['duration'] = $row->duration;
    
    $bpms = $this->ppe_song_bpm->getBPMsBySongID($sid);
    $bArr = array();
    foreach ($bpms as $b)
    {
      $bArr[] = array('beat' => $b->beat, 'bpm' => $b->bpm);
    }
    $ret['bpms'] = $bArr;
    
    $stps = $this->ppe_song_stop->getStopsBySongID($sid);
    $sArr = array();
    foreach ($stps as $s)
    {
      $sArr[] = array('beat' => $s->beat, 'time' => $s->break);
    }
    $ret['stps'] = $sArr;
    echo json_encode($ret);
  }
  
  // Load the list of edits for the specific author.
  function loadEditList()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $id = $this->uri->segment(3);
    $ret = $this->ppe_edit_edit->getSVGEdits($id);
    
    echo json_encode($ret);
  }
  
   // Load the chosen edit into the Edit Creator.
  function loadWebEdit()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $id = $this->uri->segment(3);
    $path = sprintf("%sdata/user_edits/edit_%06d.edit.gz", APPPATH, $id);
    
    $ret = $this->editparser->get_stats(gzopen($path, "r"), array('notes' => 1));
    $ret['style'] = substr($ret['style'], 5);
    echo json_encode($ret);

  }
  // Upload the edit directly to the website.
  // Should I allow title changing, and risk overriding?
  function upload()
  {
     if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $row = array();
    $eid = $this->input->post('editID');
    $row['id'] = $this->input->post('songID'); // must stay consistent.
    $row['uid'] = $this->input->post('userID');
    $row['title'] = $this->input->post('title');
    $row['style'] = "pump-" . $this->input->post('style');
    $row['diff'] = $this->input->post('diff');
    $row['steps'] = array();
    $row['steps'][] = $this->input->post('steps1');
    $row['steps'][] = $this->input->post('steps2');
    $row['jumps'] = array();
    $row['jumps'][] = $this->input->post('jumps1');
    $row['jumps'][] = $this->input->post('jumps2');
    $row['holds'] = array();
    $row['holds'][] = $this->input->post('holds1');
    $row['holds'][] = $this->input->post('holds2');
    $row['mines'] = array();
    $row['mines'][] = $this->input->post('mines1');
    $row['mines'][] = $this->input->post('mines2');
    $row['trips'] = array();
    $row['trips'][] = $this->input->post('trips1');
    $row['trips'][] = $this->input->post('trips2');
    $row['rolls'] = array();
    $row['rolls'][] = $this->input->post('rolls1');
    $row['rolls'][] = $this->input->post('rolls2');
    $row['lifts'] = array();
    $row['lifts'][] = $this->input->post('lifts1');
    $row['lifts'][] = $this->input->post('lifts2');
    $row['fakes'] = array();
    $row['fakes'][] = $this->input->post('fakes1');
    $row['fakes'][] = $this->input->post('fakes2');
    
    # Can't use <= on the below: what if it's null?
    if (!($eid > 0)) # New edit
    {
      $eid = $this->ppe_edit_edit->addEdit($row);
      $status = "New";
    }
    else
    {
      $this->ppe_edit_edit->updateEdit($eid, $row);
      $status = "Updated";
    }
    $this->db->cache_delete_all();
    $this->load->helper('twitter');
    $twit = genEditMessage($row['uid'], $this->ppe_user_user->getUserByID($row['uid']), $status);
    postTwitter($twit);
    
    $path = sprintf("%sdata/user_edits/edit_%06d.edit.gz", APPPATH, $eid);
    $fp = gzopen($path, "w");
    gzwrite($fp, $this->input->post('b64'));
    gzclose($fp);
    $ret = array();
    $ret['result'] = "successful";
    echo json_encode($ret);
  }
  
  // Download the edit created directly.
  function download()
  {
    $data = $this->input->post('b64');
    $abbr = $this->input->post('abbr');
    $style = $this->input->post('style');
    $diff = $this->input->post('diff');
    $title = $this->input->post('title');
    $name = sprintf("svg_%s_%s%d_%s.edit", $abbr, strtoupper(substr($style, 0, 1)), $diff, $title);
    
    $this->load->helper('download');
    force_download($name, $data);
  }
}