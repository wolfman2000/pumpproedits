<?php $this->load->view('global/header',
  array('css' => 'css/login.css', 'h2' => 'Log In Unsuccessful', 'title' => 'Log In Unsuccessful')); ?>
<p>The log in was unsuccessful. It seems that you are not in the system.
Please double check the username and password you entered and try again.</p>
<?php $this->load->view('login/form');
$this->load->view('global/footer');