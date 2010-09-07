<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/reset.css', 'h2' => 'Password Reset Successful', 'title' => 'Password Reset Successful')); ?>
<p>Your password was successfully reset. You are now logged in
and able to contribute edits to the website.</p>
<?php $this->load->view('global/footer');
