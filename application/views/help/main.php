<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/help.css', 'h2' => 'Account Help', 'title' => 'Account Help')); ?>
<p>
  Having trouble accessing your account? You can have a new
  password generated, or you can re-confirm your email address.
</p>
<p>
  Both options are available, and work in similar ways.
  Fill out the form, and click on the submit button.
</p>
<?php $this->load->view('help/form');
$this->load->view('global/footer');
