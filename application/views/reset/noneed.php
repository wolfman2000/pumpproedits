<?php $this->load->view('global/header',
  array('css' => 'css/login.css', 'h2' => 'No Need to Reset Password', 'title' => 'No Need to Reset Password')); ?>
<p>According to our records, you did not request to change your
password. Use your old password to log in below.</p>
<?php $this->load->view('login/form');
$this->load->view('global/footer');