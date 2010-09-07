<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/contact.css', 'h2' => 'Webmaster Contact Error', 'title' => 'Webmaster Contact Error')); ?>
<p>The email did not get sent. Something was wrong interally.
Try <a href="mailto:jafelds@gmail.com">the old fashioned way</a> for now.</p>
<?php $this->load->view('global/footer');
