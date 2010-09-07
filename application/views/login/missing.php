<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/login.css', 'h2' => 'Log In Unsuccessful', 'title' => 'Log In Unsuccessful')); ?>
<p>The log in was unsuccessful. Please fix the error and try again.</p>
<?php $this->load->view('login/form');
$this->load->view('global/footer');
