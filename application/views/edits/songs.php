<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/ ?>
<p>Below are all of the songs that have edits available.
If a song you like doesn't have an edit, feel free to 
contribute one yourself.</p>

<?php $this->load->view('edits/counter',
  array('query' => $query, 'what' => 'song'));
