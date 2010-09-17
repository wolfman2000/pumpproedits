<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/ ?>
<p>The chart does not have a file for some reason.
Try selecting the chart to preview through the form below.</p>

<?php $this->load->view('chart/editForm', array('edits' => $edits));
