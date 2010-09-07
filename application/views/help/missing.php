<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/help.css', 'h2' => 'Helping Unsuccessful', 'title' => 'Helping Unsuccessful')); ?>
<p>The help was unsuccessful. Please fix the error and try again.</p>
<?php $this->load->view('help/form');
$this->load->view('global/footer');
