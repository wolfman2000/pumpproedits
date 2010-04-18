<?php $this->load->view('global/header',
  array('css' => 'css/login.css', 'h2' => 'Log In Unsuccessful', 'title' => 'Log In Unsuccessful')); ?>
<p>The log in was unsuccessful. You are apparently banned from using
the member functions of the website. If you feel this is in error, contact
the web author.</p>
<?php $this->load->view('global/footer');