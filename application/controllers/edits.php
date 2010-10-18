<?php
/*
PHP File for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

class Edits extends Wolf_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('ppe_song_song');
		$this->load->model('ppe_user_user');
		$this->load->model('ppe_edit_edit');
	}
	
	function index()
	{
		redirect('edits/users');
	}
	
	// Show everyone that has at least one edit.
	function users()
	{
		$this->data['query'] = $this->ppe_user_user->getUsersWithEdits()->result();
		$this->_setCSS('css/edit_count.css');
		$this->_setHeader('Edits by User');
		$this->_setTitle('Edit List by User');
		$this->data['what'] = 'user';
		$this->_loadPage(array('edits/users', 'edits/counter'));
	}
	
	// The common code used to load a user's page.
	function _commonUser($uid, $title, $pages)
	{
		$this->data['const_user'] = $uid;
		$this->data['showsong'] = 1;
		$this->data['query'] = $this->ppe_edit_edit->getEditsByUser($uid)->result();
		$this->data['caption'] = $title;
		$this->_setHeader($title);
		$this->_setTitle($title);
		$this->_setCSS('css/edit_table.css');
		$this->_loadPage($pages);
	}
	
	// get all edits from the chosen user.
	function chosenUser()
	{
		$id = $this->uri->segment(2);
		if ($id == 2) redirect('edits/arcade');
		if ($id == 97) redirect('edits/arcade');
		if ($id == 113) redirect('edits/another');
		if ($id == 120) redirect('edits/mission');
		if ($id == 124) redirect('edits/gauntlet');
		
		$user = $this->ppe_user_user->getUserByID($id);
		$this->_commonUser($id, "Edits by $user", 'edits/edits');
	}
	
	// get all official edits.
	function official()
	{
		redirect('edits/arcade');
	}
	
	// Load all of the arcade style edits.
	function arcade()
	{
		$this->_commonUser(97, "Arcade Mode Edits", 'edits/edits');
	}
	
	// Load all of the another edits released by Andamiro.
	function another()
	{
		$this->_commonUser(113, "Another Edits", 'edits/edits');
	}
	
	// Load all of the mission mode charts released by Andamiro.
	function mission()
	{
		$this->_commonUser(120, "Mission Mode Edits", 'edits/edits');
	}
	
	// Load all of the gauntlet edits made by Pro 2 staff and guests.
	function gauntlet()
	{
		$this->_commonUser(124, "Gauntlet Edits", 'edits/edits');
	}
	
	// load the songs that have edits.
	function songs()
	{
		$this->data['query'] = $this->ppe_song_song->getSongsWithEdits()->result();
		$this->_setCSS('css/edit_count.css');
		$this->_setHeader('Edits by Song');
		$this->_setTitle('Edit List by Song');
		$this->data['what'] = 'song';
		$this->_loadPage(array('edits/songs', 'edits/counter'));
	}
	
	// get all edits from the chosen song.
	function chosenSong()
	{
		$id = $this->uri->segment(2);
		$page = $this->uri->segment(3, 1);
		$this->data['song'] = $this->ppe_song_song->getSongByID($id);
		$song = $this->data['song'];
		$query = $this->ppe_edit_edit->getEditsBySong($id, $page);
		$this->data['query'] = $query->result();
		
		$this->_setCSS('css/edit_table.css');
		$this->_setHeader("Edits of $song");
		$this->_setTitle("Edits of $song");
		// $this->_addJS(array('/js/jquery.pager.js', '/js/edit_song.js'));
		//TODO: Make these use a standard function.
		$this->data['const_song'] = $id;
		$this->data['showuser'] = 1;
		$this->data['caption'] = "Edits of $song";
		$this->_loadPage('edits/edits');
	}
	
	// download the chosen edit to the hard drive.
	function download()
	{
		$id = $this->uri->segment(3, false);
		// Confirm the edit isn't "deleted".
		if (!$this->ppe_edit_edit->checkExistsAndActive($id))
		{
			$this->load->helper('form');
			$this->load->model('ppe_note_skin');
			$this->load->model('ppe_note_style');
			$this->output->set_status_header(404);
			$this->data['edits'] = $this->ppe_edit_edit->getNonProblemEdits()->result_array();
			$this->data['form'] = array();
			$this->data['form']['skin'] = $this->ppe_note_skin->getSelectSkins();
			$this->data['form']['style'] = $this->ppe_note_style->getSelectStyles();
			$this->_setCSS('css/chart.css');
			$this->_setHeader('Download Unavailable');
			$this->_setTitle('Download Unavailable');
			$this->_addJS('/js/chart_edits.js');
			$this->_loadPage(array('edits/deleted', 'chart/editForm'));
			return;
		}
		
		$pro1 = $this->uri->segment(4, false);
		$name = sprintf("piu_%06d%s.edit", $id, ($pro1 !== false ? "_Pro1" : ""));
		$data = $this->ppe_edit_edit->downloadEdit($id, (boolean) $pro1);
		
		$this->load->helper('download');
		force_download($name, $data);
	}
}
