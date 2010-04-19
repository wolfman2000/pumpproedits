<?php $this->load->view('global/header',
  array('css' => 'css/help.css', 'h2' => 'Helping Unsuccessful', 'title' => 'Helping Unsuccessful')); ?>
<p>The help was unsuccessful. Please fix the error and try again.</p>
<?php $this->load->view('help/form');
$this->load->view('global/footer');