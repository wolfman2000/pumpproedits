<?php $this->load->view('global/header',
  array('css' => 'css/register.css', 'h2' => 'Registration Email not sent!', 'title' => 'Registration Email not sent!')); ?>
<p>The registration confirmation email did not get sent.
Something was wrong interally. DO NOT re-register: you did get entered
into the system. Try <a href="mailto:jafelds@gmail.com">reaching
the webmaster directly</a> for a quick way to get your account activated.</p>
<?php $this->load->view('global/footer');