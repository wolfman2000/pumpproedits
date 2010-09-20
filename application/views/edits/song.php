<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('edits/edits',
  array('query' => $songs, 'showuser' => 1, 'caption' => "Edits of $song"));
