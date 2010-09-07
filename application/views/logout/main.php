<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/main.css', 'h2' => 'Logged Out', 'title' => 'Logged Out')); ?>
<p>You have logged out successfully. Thank you for using Pump Pro Edits.</p>
<?php $this->load->view('global/footer');
