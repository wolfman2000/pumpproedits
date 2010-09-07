<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/base.css', 'h2' => 'Base Edits', 'title' => 'Base Edit Files',
  'scripts' => array('/js/jquery-1.4.2.js', '/js/jquery.pager.js', '/js/base.js'),
  'baseEdits' => $baseEdits)); ?>
<p>If you are unable to use the Edit Charter to create edits,
these .edit files are available for you. Simply select the song
and style you want.</p>
<?php $this->load->view('base/edits', array('edits' => $edits));
$this->load->view('global/footer');
