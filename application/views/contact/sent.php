<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/contact.css', 'h2' => 'Webmaster Contact Success', 'title' => 'Webmaster Contact Success')); ?>
<p>The email was sent successfully. Thank you.</p>
<?php $this->load->view('global/footer');
