<?php $this->load->view('global/header',
  array('css' => 'css/reset.css', 'h2' => 'Reset your Password', 'title' => 'Reset your Password')); ?>
<p>Use the form below to reset your password. 
Use the confirmation code that was emailed to you to confirm that you are you. </p>
<?php $this->load->view('reset/form');
$this->load->view('global/footer');