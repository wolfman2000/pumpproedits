<?php $this->load->view('global/header',
  array('css' => 'css/remove.css', 'h2' => 'Remove your Edits', 'title' => 'Remove your Edits')); ?>
<p>If you feel it's time to remove your edits from the system,
use the form below for that. Note that edit recovery may not be possible
if you change your mind, so think about it carefully.</p>
<?php $this->load->view('remove/form');
$this->load->view('global/footer');