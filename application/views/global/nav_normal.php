<nav>
<?php $uid = $this->session->userdata('id');
$this->load->view('global/mess_' . ($uid === false ? 'out' : 'in')); ?>
<ul>
<li>
<h4>Members</h4>
<ul>
<?php $this->load->view('global/memb_' . ($uid === false ? 'out' : 'in')); ?>
</ul>
</li>
<li>
<h4>Edits</h4>
<ul>
<?php if ($this->session->userdata('browser') === "Internet Explorer"): ?>
<li><a href="/base">Base Edit Files</a></li>
<?php else: ?>
<li><a href="/create">Edit Creator</a></li>
<li><a href="/chart">Edit Charter</a></li>
<?php endif; ?>
</ul>
</li>
</ul>
</nav>