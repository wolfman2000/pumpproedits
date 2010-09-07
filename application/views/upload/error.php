<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/upload.css', 'h2' => 'Upload Unsuccessful', 'title' => 'Upload Unsuccessful')); ?>
<p>The file uploaded did not seem to be an edit file.</p>

<p>Feel free to try again with a new file.</p>
<?php $this->load->view('upload/form');
$this->load->view('global/footer');
