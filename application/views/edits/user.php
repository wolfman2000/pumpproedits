<?php $this->load->view('global/header',
  array('css' => 'css/edit_table.css', 'h2' => "Edits by $user", 'title' => "Edits by $user")); ?>
<p>All of the edits of the chosen user are listed below.
Feel free to preview, download, and play.</p>

<?php $this->load->view('edits/edits',
  array('query' => $users, 'showsong' => 1, 'caption' => "Edits by $user"));
$this->load->view('global/footer');