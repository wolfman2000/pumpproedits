<?php $this->load->view('global/header',
  array('css' => 'css/upload.css', 'h2' => 'Upload Successful', 'title' => 'Upload Successful')); ?>
<p>Your edit was uploaded successfully.
<?php echo anchor("/user/" . $this->session->userdata('id'), "View your work here."); ?></p>

<p>If you wish to upload another file, you can do so below.</p>
<?php $this->load->view('upload/form');
$this->load->view('global/footer');