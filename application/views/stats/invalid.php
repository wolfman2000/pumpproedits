<?php $this->load->view('global/header',
  array('css' => 'css/stats.css', 'h2' => 'Edit Stats Not Gotten', 'title' => 'Edit Stats Not Gotten')); ?>
<p>The edit file could not be parsed due to this reason:</p>
<p class="error_list"><?php echo $e->message; ?></p>

<p>Fix up the file, then feel free to try again.</p>
<?php $this->load->view('stats/form');
$this->load->view('global/footer');