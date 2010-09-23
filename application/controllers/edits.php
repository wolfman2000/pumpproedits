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
		$this->load->library('pagination');
	}
	
	function index()
	{
		redirect('edits/users');
	}
	
	# Add the common pager settings here.
	function _pagerSetup($config)
	{
		$config['per_page'] = APP_MAX_EDITS_PER_PAGE;
		$config['cur_tag_open'] = '<strong>';
		$config['cur_tag_close'] = '</strong>';
		$config['full_tag_open'] = '<p class="pager">';
		$config['full_tag_close'] = '</p>';
		$config['first_link'] = '«';
		$config['last_link'] = '»';
		return $config;
	}
	
	function users()
	{
		$this->data['query'] = $this->ppe_user_user->getUsersWithEdits()->result();
		$this->_setCSS('css/edit_count.css');
		$this->_setHeader('Edits by User');
		$this->_setTitle('Edit List by User');
		$this->data['what'] = 'user';
		$this->_loadPage(array('edits/users', 'edits/counter'));
	}
	
	// get all edits from the chosen user.
	function chosenUser()
	{
		$id = $this->uri->segment(2);
		$page = $this->uri->segment(3, 1);
		$this->data['user'] = $this->ppe_user_user->getUserByID($id);
		$user = $this->data['user'];
		$query = $this->ppe_edit_edit->getEditsByUser($id, $page);
		$this->data['query'] = $query->result();
		
		// a lot of the code below is temporary.
		$config['base_url'] = sprintf('http://%s/user/%d/', $this->input->server('SERVER_NAME'), $id);
		$total = $this->ppe_edit_edit->getUserEditCount($id);
		$config['total_rows'] = $total;
		$this->data['maxEdits'] = $total;
		$this->pagination->initialize($this->_pagerSetup($config));
		
		$this->_setCSS('css/edit_table.css');
		$this->_setHeader("Edits by $user");
		$this->_setTitle("Edits by $user");
		// $this->_addJS(array('/js/jquery.pager.js', '/js/edit_user.js'));
		//TODO: Make these use a standard function.
		$this->data['const_user'] = $id;
		$this->data['showsong'] = 1;
		$this->data['caption'] = "Edits by $user";
		$this->_loadPage('edits/edits');
	}
	
	// get up to (10) of a user's edits via AJAJ.
	function userConquer()
	{
		if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
		{
			return;
		}
		header("Content-Type: application/json");
		$ret = array();
		$user = $this->uri->segment(3);
		$page = $this->uri->segment(4, 1);
		$ret['edits'] = $this->ppe_edit_edit->getEditsByUser($user, $page)->result_array();
		echo json_encode($ret);
	}
	
	// get all official edits.
	function official()
	{
		$id = 2;
		$page = $this->uri->segment(2, 1);
		$query = $this->ppe_edit_edit->getEditsByUser($id, $page);
		$this->data['query'] = $query->result();
		
		// a lot of the code below is temporary.
		$config['base_url'] = sprintf('http://%s/official/', $this->input->server('SERVER_NAME'));
		$total = $this->ppe_edit_edit->getUserEditCount($id);
		$config['total_rows'] = $total;
		$this->data['maxEdits'] = $total;
		$this->pagination->initialize($this->_pagerSetup($config));
		
		$this->_setCSS('css/edit_table.css');
		$this->_setHeader("Official Edits");
		$this->_setTitle("Official Edits");
		// $this->_addJS(array('/js/jquery.pager.js', '/js/edit_user.js'));
		$this->data['const_user'] = 2;
		
		$this->data['showsong'] = 1;
		$this->data['caption'] = "Official";
		
		$this->_loadPage(array('edits/official', 'edits/edits'));
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
	
	// get up to (10) of a song's edits via AJAJ.
	function songConquer()
	{
		if (!(isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
			strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'))
		{
			return;
		}
		header("Content-Type: application/json");
		$ret = array();
		$song = $this->uri->segment(3);
		$page = $this->uri->segment(4, 1);
		$ret['edits'] = $this->ppe_edit_edit->getEditsBySong($song, $page)->result_array();
		echo json_encode($ret);
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
		
		// a lot of the code below is temporary.
		$config['base_url'] = sprintf('http://%s/song/%d/', $this->input->server('SERVER_NAME'), $id);
		$total = $this->ppe_edit_edit->getSongEditCount($id);
		$config['total_rows'] = $total;
		$this->data['maxEdits'] = $total;
		$this->pagination->initialize($this->_pagerSetup($config));
		
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
