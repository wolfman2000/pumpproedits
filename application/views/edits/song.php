<?php $this->load->view('global/header',
  array('css' => 'css/edit_table.css', 'h2' => "Edits of $song", 'title' => "Edits of $song")); ?>
<p>All of the edits of the chosen song are listed below.
Feel free to preview, download, and play.</p>

<?php $this->load->view('edits/edits',
  array('query' => $songs, 'showuser' => 1, 'caption' => "Edits of $song"));
$this->load->view('global/footer');