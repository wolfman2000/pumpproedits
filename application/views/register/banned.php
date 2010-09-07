<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/register.css', 'h2' => 'Registration Unsuccessful', 'title' => 'Registration Unsuccessful')); ?>
<p>The registration was unsuccessful. You are apparently banned from using
the member functions of the website. If you feel this is in error, contact
the web author.</p>
<?php $this->load->view('global/footer');
