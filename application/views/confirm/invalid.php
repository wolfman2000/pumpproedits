<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/confirm.css', 'h2' => 'Confirmation Unsuccessful', 'title' => 'Confirmation Unsuccessful')); ?>
<p>The confirmation was unsuccessful. Double check both
the confirmation code and password, then try again.</p>
<?php $this->load->view('confirm/form');
$this->load->view('global/footer');
