<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$scripts = array();
$this->load->view('global/header',
  array('h2' => 'Edit Creator Unvailable', 'title' => 'Edit Creator Unavailable', )); ?>
<h3>Error!</h3>
<p>Your web browser cannot run the Edit Creator.
Either update to the latest version to see if that will work,
or switch to a different web browser.</p>
<p>The list of supported browsers is located up top if you are
using a graphical client.</p>
<?php $this->load->view('global/footer');
