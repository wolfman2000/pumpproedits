<?php $this->load->view('global/header',
  array('css' => 'css/remove.css', 'h2' => 'Edits Removed', 'title' => 'Edits Removed')); ?>
<p>The selected edits have been removed from the system.
If you wish to remove more, use the form below.</p>
<?php $this->load->view('remove/form');
$this->load->view('global/footer');