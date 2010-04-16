<?php $this->load->view('global/header',
  array('css' => 'css/base.css', 'h2' => 'Base Edits', 'title' => 'Base Edit Files')); ?>
<p>If you are unable to use the Edit Charter to create edits,
these .edit files are available for you. Simply select the song
and style you want.</p>
<?php $this->load->view('base/edits', array('page' => $page, 'edits' => $edits));
$this->load->view('global/footer');