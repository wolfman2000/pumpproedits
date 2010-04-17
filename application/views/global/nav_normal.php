<nav>
<p>Make your selection below and have fun.</p>
<ul>
<li>
<h4>Edits</h4>
<ul>
<li><?php echo anchor("/base", "Base Edit Files"); ?></li>
<li><?php echo anchor("/stats", "Edit Stat Getter"); ?></li>
<?php if ($this->session->userdata('browser') !== "Internet Explorer"): ?>
<li><?php echo anchor("/chart", "Edit Charter"); ?></li>
<?php endif; ?>
<li><?php echo anchor("/songs", "Edit List by Song"); ?></li>
<li><?php echo anchor("/users", "Edit List by User"); ?></li>
</ul>
</li>
<li>
<h4>Everyone</h4>
<ul>
<?php if ($this->session->userdata('browser') !== "Internet Explorer"): ?>
<li><?php echo anchor("/chart/songs", "Official Stepcharts"); ?></li>
<?php endif; ?>
<li><?php echo anchor("/contact", "Contact"); ?></li>
<li><?php echo anchor("/thanks", "Credits/Thanks"); ?></li>
</ul>
</li>
</ul>
</nav>
