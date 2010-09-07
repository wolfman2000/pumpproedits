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
    $this->load->model('ppe_song_section');
    $this->load->model('ppe_edit_edit');
    $this->load->model('ppe_edit_measure');
    $this->load->model('ppe_play_style');
    $this->load->library('EditParser');
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
    header("Content-Type: application/xhtml+xml");
    $data = array();
    $data['songs'] = $this->ppe_song_song->getSongsWithGame()->result();
    $data['andy'] = 0;
    $data['others'] = 0;
    $id = $this->session->userdata('id');
    $data['loads'] = array();
    $data['peeps'] = array();
    $data['loads'][] = array('id' => 'hd', 'value' => 'Load edit from hard drive.');
    
    if ($id)
    {
      $data['andy'] = $this->ppe_user_power->canEditOfficial($id);
      $data['others'] = $this->ppe_user_power->canEditOthers($id);
      $data['loads'][] = array('id' => $id, 'value' => 'Load one of my web site edits.');
      if ($data['andy'])
      {
        $data['loads'][] = array('id' => 2, 'value' => 'Load an official web site edit.');
        if ($data['others'])
        {
          $data['loads'][] = array('id' => 'off', 'value' => 'Load an official stepchart.');
          $data['loads'][] = array('id' => 'all', 'value' => "Load someone else's edit...carefully.");
          $data['peeps'] = $this->ppe_user_user->getOtherUsers(array($id, 2, 95));
        }
      }
    
    }
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
    }
    catch (Exception $e)
    {
      $ret['exception'] = $e->getMessage();
    }
    @unlink($fn);
    echo json_encode($st);
  }
  
  // Give the user help upon request.
  function help()
  {
    $this->load->view('create/help');
  }
  
  // Get the possible difficulties for each song that comes in.
  function songDifficulties()
  {
  	if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $sid = $this->uri->segment(3);
    $ret = array();
    $ret[] = array("value" => "", "label" => "Choose!");
    foreach ($this->ppe_song_game->getValidDifficulties($sid)->result() as $q)
    {
    	$ret[] = array("value" => $q->style, "label" => "pump-" . $q->style);
    }
    
    echo json_encode($ret);
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
  
  // a common AJAJ function to get song sync data.
  function _songData($sid)
  {
    $row = $this->ppe_song_song->getCreatorData($sid);
    $ret['name'] = $row->name;
    $ret['abbr'] = $row->abbr;
    $ret['measures'] = intval($row->measures);
    $ret['duration'] = ($row->duration ? floatval($row->duration) : 90);
    
    $bpms = $this->ppe_song_bpm->getBPMsBySongID($sid);
    $bArr = array();
    
    foreach ($bpms as $b)
    {
      $bArr[] = array('beat' => floatval($b->beat), 'bpm' => ($b->bpm ? floatval($b->bpm) : '?'));
    }
    $ret['bpms'] = $bArr;
    
    $stps = $this->ppe_song_stop->getStopsBySongID($sid);
    $sArr = array();
    foreach ($stps as $s)
    {
      $sArr[] = array('beat' => floatval($s->beat), 'time' => ($s->break ? floatval($s->break) : '?'));
    }
    $ret['stps'] = $sArr;
    
    $secs = $this->ppe_song_section->getSectionsBySongID($sid);
    $sArr = array();
    foreach ($secs as $s)
    {
      $sArr[] = array('beat' => floatval($s->beat), 'measure' => floor($s->beat / 4) + 1, 'section' => $s->section);
    }
    $ret['secs'] = $sArr;
    return $ret;
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
    $ret = $this->_songData($sid);
    $ret['difficulty'] = "Edit";
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
    $ret = $this->ppe_edit_edit->getEditChartStats($id);
    $ret['notes'] = $this->ppe_edit_measure->getCreatorNotes($id)->result();
    $ret['authID'] = $this->ppe_edit_edit->getEditAuthor($id);
    $ret['songData'] = $this->_songData($ret['song_id']);
    echo json_encode($ret);

  }
  // Load the official chart...if it exists. If it does not, say so.
  function loadOfficial()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $id = $this->uri->segment(3);
    $diff = $this->uri->segment(4);
    $ret = array();
    // Make doubly sure the song exists.
    if (!$this->ppe_song_song->getSongByID($id))
    {
      $ret['error'] = "The chosen song does not exist.";
    }
    elseif (!in_array($diff, array('ez', 'nr', 'hr', 'cz', 'fs', 'hd', 'nm', 'rt')))
    {
      $ret['error'] = "The difficulty chosen was not valid.";
    }
    if (!count($ret))
    {
      // Try to read the file. If it doesn't exist, that's alright.
      $path = sprintf("%sdata/official/%d_%s.sm.gz", APPPATH, $id, $diff);
      if (file_exists($path))
      {
        $data = array('notes' => 1, 'strict_song' => 0, 'arcade' => $diff);
        $ret = $this->editparser->get_stats(gzopen($path, "r"), $data);
        //$ret['difficulty'] = $this->editparser->getSMDiff($diff);
      }
      else
      {
        $ret['dShort'] = $diff;
        // At least get the style sorted out.
        if (in_array($diff, array('ez', 'nr', 'hr', 'cz')))
        {
          $ret['style'] = "single";
        }
        elseif (in_array($diff, array('fs', 'nm')))
        {
          $ret['style'] = "double";
        }
        elseif ($diff == "hd")
        {
          $ret['style'] = "halfdouble";
        }
        else // routine
        {
          $ret['style'] = "routine";
        }
        // Put in defaults to keep the code flowing.
        $ret['notes'] = null;
        $ret['author'] = null;
        $ret['difficulty'] = $this->editparser->getSMDiff($diff);
      }
      $data = $this->_songData($id);
      foreach ($data as $k => $v)
      {
        $ret[$k] = $v;
      }
    }
    echo json_encode($ret);
  }
  
  // Upload the official chart to the website.
  function uploadOfficial()
  {
    if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
      strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
    {
      return;
    }
    header("Content-Type: application/json");
    $row = array();
    /*
    // Double check the hijacking variable.
    $checker = $this->input->post('but_sub');
    if ($checker !== "songSubmit")
    {
      $ret['error'] = "You came to this process illegally. It must be stopped.";
      echo json_encode($ret);
      return;
    }
    */
    // Make DOUBLY sure the user can upload the edit.
    $id = $this->session->userdata('id');
    if (!$this->ppe_user_power->canEditOthers($id))
    {
      $ret['error'] = "You don't have permission to upload an official chart.";
      echo json_encode($ret);
      return;
    }
    
    // Ensure the difficulty variable wasn't changed...easily.
    $dshort = strtolower($this->input->post('dShort'));
    $valid = array('ez', 'nr', 'hr', 'cz', 'fs', 'hd', 'nm', 'rt');
    if (!in_array($dshort, $valid))
    {
      $ret['error'] = "An invalid difficulty was detected. This chart can't be uploaded.";
      echo json_encode($ret);
      return;
    }
    $songid = $this->input->post('songID'); // song ID
    $dshort = $this->input->post('dShort'); // two letter difficulty
    $style = "pump-" . $this->input->post('style'); // traditional SM style.
    $sd = $this->input->post('difficulty'); // traditional SM difficulty.
    $diff = strtolower($this->editparser->getOfficialStyle($style, $sd)); // needed for query below.
    
    // Place the record in the database if it doesn't exist.
    $this->load->model('ppe_song_difficulty');
    $this->ppe_song_difficulty->addChart($songid, $diff);
    
    // Finally place the edit in its place.
    $path = sprintf("%sdata/official/%d_%s.sm.gz", APPPATH, $songid, $dshort);
    $fp = gzopen($path, "w");
    gzwrite($fp, $this->input->post('b64'));
    gzclose($fp);
    
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
    $song = $this->ppe_song_song->getSongByID($row['id']);
    $row['uid'] = $this->input->post('userID');
    $row['title'] = $this->input->post('title');
    $st = $this->input->post('style');
    $row['style'] = "pump-" . $st;
    $row['style_id'] = $this->ppe_play_style->getPlayStyleID($st);
    $row['diff'] = $this->input->post('diff');
    $row['public'] = ($this->input->post('public') == 1 ? 1 : 0);
    
    // See if any OTHER edits have the same title and style.
    if ($eid)
    {
      $dupes = $this->ppe_edit_edit->checkDuplicates($row['id'], $row['uid'],
        $this->input->post('style'), $row['title'], $eid);
    }
    else
    {
      $dupes = $this->ppe_edit_edit->checkDuplicates($row['id'], $row['uid'],
        $this->input->post('style'), $row['title']);
    }
    if ($dupes)
    {
      $ret['result'] = "duplicate";
      echo json_encode($ret);
      return;
    }
    
    $radars = explode('_', $this->input->post('radar'), 10);
    $row['stream'] = array();
    $row['voltage'] = array();
    $row['air'] = array();
    $row['freeze'] = array();
    $row['chaos'] = array();
    $row['stream'][] = $radars[0];
    $row['voltage'][] = $radars[1];
    $row['air'][] = $radars[2];
    $row['freeze'][] = $radars[3];
    $row['chaos'][] = $radars[4];
    if ($row['style'] === "pump-routine")
    {
    	$row['stream'][] = $radars[5];
		$row['voltage'][] = $radars[6];
		$row['air'][] = $radars[7];
		$row['freeze'][] = $radars[8];
		$row['chaos'][] = $radars[9];
    }
    
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
    
    $row['notes'] = json_decode($this->input->post('notes'), true);
    
    # Can't use <= on the below: what if it's null?
    if (!($eid > 0)) # New edit
    {
      $eid = $this->ppe_edit_edit->addEdit($row);
      $status = "created";
    }
    else
    {
      $this->ppe_edit_edit->updateEdit($eid, $row);
      $status = "updated";
    }
    $this->db->cache_delete_all();
    if ($row['public'])
    {
    	$this->load->library('OAuth');
    	$twit = $this->oauth->genEditMessage($row['uid'],
        $this->ppe_user_user->getUserByID($row['uid']),
        $status, $row['style'], $row['title'], $song);
        $this->oauth->postTwitter($twit);

    }
    $path = sprintf("%sdata/user_edits/edit_%06d.edit.gz", APPPATH, $eid);
    $fp = gzopen($path, "w");
    gzwrite($fp, $this->input->post('b64'));
    gzclose($fp);
    $ret = array();
    $ret['result'] = "successful";
    $ret['editid'] = $eid;
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
