<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/help.css', 'h2' => 'Email not sent!', 'title' => 'Email not sent!')); ?>
<p>The email did not get sent. Something was wrong interally.
DO NOT refresh the browser: You may get a different message.
Try <a href="mailto:jafelds@gmail.com">reaching
the webmaster directly</a> for a quick way to get your account fixed.</p>
<?php $this->load->view('global/footer');
