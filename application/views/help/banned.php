<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/help.css', 'h2' => 'Help Unsuccessful', 'title' => 'Help Unsuccessful')); ?>
<p>Help is not possible here. You are apparently banned from using
the member functions of the website. If you feel this is in error, contact
the web author.</p>
<?php $this->load->view('global/footer');
