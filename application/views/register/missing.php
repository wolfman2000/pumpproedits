<?php $this->load->view('global/header',
  array('css' => 'css/register.css', 'h2' => 'Registration Unsuccessful', 'title' => 'Registration Unsuccessful')); ?>
<p>The registration was unsuccessful. Please fix the error and try again.</p>
<?php $this->load->view('register/form');
$this->load->view('global/footer');