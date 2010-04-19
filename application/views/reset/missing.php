<?php $this->load->view('global/header',
  array('css' => 'css/reset.css', 'h2' => 'Password Resetting Unsuccessful', 'title' => 'Password Resetting Unsuccessful')); ?>
<p>The password resetting was unsuccessful. Please fix the error and try again.</p>
<?php $this->load->view('reset/form');
$this->load->view('global/footer');