<?php $this->load->view('global/header',
  array('css' => 'css/chart.css', 'h2' => 'Edit Chart Generator', 'title' => 'Edit Chart Generator')); ?>
<p>Select the edit you want to see a chart of. You can
control what the chart looks like with the other options.</p>

<?php $this->load->view('chart/editForm', array('edits' => $edits, 'form' => $form));
$this->load->view('global/footer');