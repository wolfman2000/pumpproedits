<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Chart extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->library('form_validation');
		$this->form_validation->set_error_delimiters('<p class="error_list">', '</p>');
		$this->load->model('ppe_edit_edit');
		$this->load->model('ppe_song_song');
		$this->load->model('ppe_user_user');
		$this->load->model('ppe_note_skin');
		$this->load->model('ppe_note_style');
		$this->difficulties = array('ez', 'nr', 'hr', 'cz', 'hd', 'fs', 'nm', 'rt');
		$this->_setCSS('css/chart.css');
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
		if (in_array($str, $this->ppe_note_style->getNoteStyles(1))) return true;
		$this->form_validation->set_message('_notecolor_exists', "Please choose a valid note style.");
		return false;
	}
	
	// confirm the note skin exists.
	function _noteskin_exists($str)
	{
		if (in_array($str, $this->ppe_note_skin->getNoteSkins(1))) return true;
		$this->form_validation->set_message('_noteskin_exists', "Please choose a valid note skin.");
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
	
	function index()
	{
		redirect('chart/edits');
	}
	
	function _showEditForm($first, $header)
	{
		$this->data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
		$this->data['form'] = array();
		$this->data['form']['skin'] = $this->ppe_note_skin->getSelectSkins();
		$this->data['form']['style'] = $this->ppe_note_style->getSelectStyles();
		$this->_addJS('/js/chart_edits.js');
		$this->_setHeader($header);
		$this->_setTitle($header);
		$this->_loadPage(array($first, 'chart/editForm'));
	}
	
	function edits()
	{
		$this->_showEditForm('chart/edits', 'Edit Chart Generator');
	}
	
	// Use this common GET function to show the edit.
	function showEdit()
	{
		$eid = $this->uri->segment(3, -1);
		// Confirm the edit isn't "deleted".
		if (!$this->ppe_edit_edit->checkExistsAndActive($eid))
		{
			$this->output->set_status_header(404);
			$this->_showEditForm('chart/deleted', 'No Edit Chart');
			return;
		}
		
		$author = $this->ppe_user_user->getUserByEditID($eid);
		$notedata = $this->ppe_edit_edit->getEditChartStats($eid);
		
		$p = array
		(
			'kind' => $this->uri->segment(4, 'classic'),
			'red4' => $this->uri->segment(5, 0),
			'noteskin' => $this->uri->segment(6, 'original'),
			'speed_mod' => $this->uri->segment(7, 2),
			'mpcol' => $this->uri->segment(8, 6),
			'scale' => $this->uri->segment(9, 1),
			'cols' => $notedata['cols'],
			'eid' => $eid,
			);
		$this->load->library('EditCharter', $p);
		$notedata['author'] = $author;
		$notedata['notes'] = false;
		header("Content-Type: application/xhtml+xml");
		$xml = $this->editcharter->genChart($notedata);
		echo str_replace("xml:id", "id", $xml->saveXML());
	}
	
	function editProcess()
	{
		if ($this->form_validation->run() === FALSE)
		{
			$this->_showEditForm('chart/editErorr', 'Edit Chart Error');
			return;
		}
		
		$url = sprintf("/chart/showEdit/%d/%s/%s/%s/%1.2f/%d/%1.2f",
			$this->input->post('edits'),
			$this->input->post('kind'),
			$this->input->post('red4'),
			$this->input->post('noteskin'),
			$this->input->post('speed'),
			$this->input->post('mpcol'),
			$this->input->post('scale')
			);
		redirect($url, 'location', 303);
		return;
	}
	
	// get the list of songs for possible chart previewing.
	function songs()
	{
		$this->data['songs'] = $this->ppe_song_song->getSongsWithGameAndDiff()->result_array();
		$this->data['form'] = array();
		$this->data['form']['skin'] = $this->ppe_note_skin->getSelectSkins();
		$this->data['form']['style'] = $this->ppe_note_style->getSelectStyles();
		$this->_addJS('/js/official.js');
		$this->_setHeader('Official Chart Generator');
		$this->_setTitle('Official Chart Generator');
		$this->_loadPage(array('chart/songs', 'chart/songForm'));
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
			$this->data['songs'] = $this->ppe_song_song->getSongsWithGameAndDiff()->result_array();
			$this->data['form'] = array();
			$this->data['form']['skin'] = $this->ppe_note_skin->getSelectSkins();
			$this->data['form']['style'] = $this->ppe_note_style->getSelectStyles();
			$this->_addJS('/js/official.js');
			$this->_setHeader('Official Chart Generator');
			$this->_setTitle('Official Chart Generator');
			$this->_loadPage(array('chart/songError', 'chart/songForm'));
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
			'mpcol' => $this->input->post('mpcol'), 'scale' => $this->input->post('scale'),
			'arcade' => 1, 'noteskin' => $this->input->post('noteskin'));
		$this->load->library('SongCharter', $p);
		header("Content-Type: application/xhtml+xml");
		$xml = $this->songcharter->genChart($notedata);
		echo str_replace("xml:id", "id", $xml->saveXML());
	}
	
	function quick()
	{
		redirect(sprintf("/chart/showEdit/%d/%s/blue/original/2/6/1",
			$this->uri->segment(3, -1),
			$this->uri->segment(4, 'classic')
			), 'location', 303);
		return;
	}
}
