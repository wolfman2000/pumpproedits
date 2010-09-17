<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/ ?>
<p>Select the song and difficulty you want to see a chart of.</p>

<?php $this->load->view('chart/songForm', array('songs' => $songs, 'form' => $form));
