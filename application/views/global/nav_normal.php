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
<li><?php echo anchor("/base", "Base Edit Files"); ?></li>
<li><?php echo anchor("/stats", "Edit Stat Getter"); ?></li>
<?php else: ?>
<li><?php echo anchor("/create", "Edit Creator"); ?></li>
<li><?php echo anchor("/chart", "Edit Charter"); ?></li>
<?php endif; ?>
<li><?php echo anchor("/songs", "Edit List by Song"); ?></li>
<li><?php echo anchor("/users", "Edit List by User"); ?></li>
<li><?php echo anchor("/official", "Official Chart Edits"); ?></li>
</ul>
</li>
<li>
<h4>Everyone</h4>
<ul>
<li><a href="/blog">Pump Pro Edits Blog</a></li>
<?php if ($this->session->userdata('browser') !== "Internet Explorer"): ?>
<li><?php echo anchor("/chart/songs", "Official Stepcharts"); ?></li>
<?php endif; ?>
<li><?php echo anchor("/usb", "USB Usage"); ?></li>
<li><?php echo anchor("/contact", "Contact"); ?></li>
<li><?php echo anchor("/thanks", "Credits/Thanks"); ?></li>
</ul>
</li>
</ul>
</nav>
