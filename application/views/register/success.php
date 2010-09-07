<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/register.css', 'h2' => 'Almost Registered!', 'title' => 'Almost Registered!')); ?>
<p>An email confirmation message has been sent to your email address.
Make sure to read it and follow the instructions to get confirmed!</p>
<?php $this->load->view('global/footer');
