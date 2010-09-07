<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/stats.css', 'h2' => 'Edit Stats Not Gotten', 'title' => 'Edit Stats Not Gotten')); ?>
<p>The file uploaded did not seem to be an edit file.</p>

<p>Feel free to try again with a new file.</p>
<?php $this->load->view('stats/form');
$this->load->view('global/footer');
