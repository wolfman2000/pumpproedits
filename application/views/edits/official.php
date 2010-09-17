<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/ ?>
<p>These are all of the charts that the official people,
such as Andamiro or Fun In Motion, made for in any of the
Pump It Up games. They can be Anothers, old charts,
World Max missions, or anything in between.
Feel free to preview, download, and play.</p>

<?php $this->load->view('edits/edits',
  array('query' => $users, 'showsong' => 1, 'caption' => "Official"));
