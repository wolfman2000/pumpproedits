<?php $this->load->view('global/header',
  array('css' => 'css/chart.css', //'scripts' => array('/js/jquery-1.4.2.js', '/js/official.js'),
  'h2' => 'No Edit Chart', 'title' => 'No Edit Chart')); ?>
<p>The chart does not have a file for some reason.
Try selecting the chart to preview through the form below.</p>

<?php $this->load->view('chart/editForm', array('edits' => $edits));
$this->load->view('global/footer');