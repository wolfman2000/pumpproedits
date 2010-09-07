<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/reset.css', 'h2' => 'Password Resetting Unsuccessful', 'title' => 'Password Resetting Unsuccessful')); ?>
<p>The password resetting was unsuccessful. Please fix the error and try again.</p>
<?php $this->load->view('reset/form');
$this->load->view('global/footer');
