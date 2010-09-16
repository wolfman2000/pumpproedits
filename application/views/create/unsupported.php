<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$scripts = array();
$this->load->view('global/header',
  array('css' => 'css/create.css', 'h2' => 'Edit Creator', 'title' => 'Edit Creator',
  )); ?>
<h3>Error!</h3>
<p>Your web browser cannot run the Edit Creator.
Either update to the latest version or get a
different web browser.</p>

<?php $this->load->view('global/footer');
