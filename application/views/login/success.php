<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/login.css', 'h2' => 'Log In Successful', 'title' => 'Log In Successful')); ?>
<p>You are now logged in. Have fun contributing edits! ☺</p>
<?php $this->load->view('global/footer');
