<?php $this->load->view('global/header',
  array('css' => 'css/edit_table.css', 'h2' => "Official Edits", 'title' => "Official Edits",
  'scripts' => array('/js/jquery-1.4.2.js', '/js/jquery.pager.js', '/js/edit_official.js'),
  'maxEdits' => $maxEdits, 'const_user' => 2)); ?>
<p>These are all of the charts that the official people,
such as Andamiro or Fun In Motion, made for in any of the
Pump It Up games. They can be Anothers, old charts,
World Max missions, or anything in between.
Feel free to preview, download, and play.</p>

<?php $this->load->view('edits/edits',
  array('query' => $users, 'showsong' => 1, 'caption' => "Official"));
$this->load->view('global/footer');