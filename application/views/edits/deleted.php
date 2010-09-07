<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/chart.css', //'scripts' => array('/js/jquery-1.4.2.js', '/js/official.js'),
  'h2' => 'Download Unavailable', 'title' => 'Download Unavailable')); ?>
<p>The edit you requested is no longer available for download.
You can preview different charts below to decide which one to download later.</p>

<?php $this->load->view('chart/editForm', array('edits' => $edits));
$this->load->view('global/footer');
