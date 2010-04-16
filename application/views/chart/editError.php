<?php $this->load->view('global/header',
  array('css' => 'css/chart.css', 'h2' => 'Edit Chart Error', 'title' => 'Edit Chart Error')); ?>
<p>An error took place during processing. Read the error,
make your adjustments, and try again.</p>

<?php $this->load->view('chart/editForm', array('edits' => $edits));
$this->load->view('global/footer');