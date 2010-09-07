<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/base.css', 'h2' => 'Base Edit Error', 'title' => 'Base Edit Error')); ?>
<p>The chosen song does not exist.</p>
<?php $this->load->view('global/footer');
