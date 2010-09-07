<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/confirm.css', 'h2' => 'Confirmation Successful', 'title' => 'Confirmation Successful')); ?>
<p>You have confirmed your account. You are now logged in, and thus are able
to start submitting edits for everyone to see. ☺</p>
<?php $this->load->view('global/footer');
