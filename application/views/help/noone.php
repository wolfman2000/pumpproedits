<?php $this->load->view('global/header',
  array('css' => 'css/register.css', 'h2' => 'New User', 'title' => 'New user')); ?>
<p>You appear to be a new user. You can register below.</p>
<?php $this->load->view('register/form');
$this->load->view('global/footer');