<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/confirm.css', 'h2' => 'Confirm your Account', 'title' => 'Confirm your Account')); ?>
<p>You are almost able to submit edits and contribute to the website!</p>
<p>Just fill in the form below with your password. If you came to this page
through the navigation links instead of your email message, you will have to
enter your confirmation code as well.</p>
<?php $this->load->view('confirm/form');
$this->load->view('global/footer');
