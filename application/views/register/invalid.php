<?php $this->load->view('global/header',
  array('css' => 'css/register.css', 'h2' => 'Registration Unsuccessful', 'title' => 'Registration Unsuccessful')); ?>
<p>The registration was unsuccessful. The issues are below:</p>
<ul>
<?php foreach ($errors as $e): ?>
<li class="error_list"><?php echo $e; ?></li>
<?php endforeach; ?>
</ul>
<p>Please fix these errors and try again.</p>
<?php $this->load->view('register/form');
$this->load->view('global/footer');