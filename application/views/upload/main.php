<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/upload.css', 'h2' => 'Upload Edit', 'title' => 'Upload Edit')); ?>
<p>For those that can't use the Edit Creator to create and upload
edits, this form will also work. Just find the file
on your hard drive and hit the submit button. Please, only
submit edits that you made.
</p>
<p>This same form is used for uploading new and updated edits.
As a general rule, you cannot have two edits of the same
song and style (single or double) have the same edit title,
even if the step content is different. If you do this, the old
edit will be lost forever.</p>
<?php $this->load->view('upload/form');
$this->load->view('global/footer');
