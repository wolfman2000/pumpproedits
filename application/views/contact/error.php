<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/contact.css', 'h2' => 'Webmaster Contact Error', 'title' => 'Webmaster Contact Error')); ?>
<p>The email did not get sent. Something may have been wrong
with the form values. Double check those and try again unless
the error message says otherwise.</p>
<?php $this->load->view('contact/form');
$this->load->view('global/footer');
