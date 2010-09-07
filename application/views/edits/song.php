<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/edit_table.css', 'h2' => "Edits of $song", 'title' => "Edits of $song",
  'scripts' => array('/js/jquery.pager.js', '/js/edit_song.js'),
  'maxEdits' => $maxEdits, 'const_song' => $this->uri->segment(2)));
$this->load->view('edits/edits',
  array('query' => $songs, 'showuser' => 1, 'caption' => "Edits of $song"));
$this->load->view('global/footer');
