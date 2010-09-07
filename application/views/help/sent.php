<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/help.css', 'h2' => 'Email on the way', 'title' => 'Email on the way')); ?>
<p>An email is on its way to your email address to help
take care of your account issue. Hang in there: you're almost done!</p>
<?php $this->load->view('global/footer');
