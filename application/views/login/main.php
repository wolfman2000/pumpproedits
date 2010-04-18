<?php $this->load->view('global/header',
  array('css' => 'css/login.css', 'h2' => 'Log In', 'title' => 'Log In')); ?>
<p>Use the form below to log in and be able to submit edits.</p>
<?php $this->load->view('login/form');
$this->load->view('global/footer');