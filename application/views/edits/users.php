<?php $this->load->view('global/header',
  array('css' => 'css/edit_count.css', 'h2' => 'Edits by User', 'title' => 'Edit List by User')); ?>
<p>Below are all of the users that have edits available.</p>

<?php $this->load->view('edits/counter',
  array('query' => $query, 'what' => 'user'));
$this->load->view('global/footer');