<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/edit_table.css', 'h2' => "Edits by $user", 'title' => "Edits by $user",
  'scripts' => array('/js/jquery.pager.js', '/js/edit_user.js'),
  'maxEdits' => $maxEdits, 'const_user' => $this->uri->segment(2)));
$this->load->view('edits/edits',
  array('query' => $users, 'showsong' => 1, 'caption' => "Edits by $user"));
$this->load->view('global/footer');
