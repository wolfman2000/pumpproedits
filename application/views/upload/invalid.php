<?php $this->load->view('global/header',
  array('css' => 'css/upload.css', 'h2' => 'Upload Unsuccessful', 'title' => 'Upload Unsuccessful')); ?>
<p>The edit file could not be parsed due to this reason:</p>
<p class="error_list"><?php echo $e->message; ?></p>

<p>Fix up the file, then feel free to try again.</p>
<?php $this->load->view('upload/form');
$this->load->view('global/footer');