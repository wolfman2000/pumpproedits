
<nav id="main_nav" class="grid_12">
<?php $uid = $this->session->userdata('id');

# $this->load->view('global/mess_' . ($uid === false ? 'out' : 'in')); ?>
<ul class="sf-menu">
<li><?php echo anchor("/", "Home"); ?></li>
<?php if (!$uid and $this->agent->is_browser() and $this->session->userdata('browser') !== "Internet Explorer"): ?>
<li><?php echo anchor("#", "Account Stuff"); ?><ul>
<li><?php echo anchor("/confirm", "Confirm Account"); ?></li>
<li><?php echo anchor("/help", "Account Help"); ?></li>
<li><?php echo anchor("/reset", "Reset Password"); ?></li>
</ul></li>
<?php endif; ?>
<li><?php echo anchor("#", "Browse Edits"); ?><ul>
<li><?php echo anchor("/songs", "List by Song"); ?></li>
<li><?php echo anchor("/users", "List by Author"); ?></li>
<li><?php echo anchor("/official", "List Official Edits"); ?></li>
<?php if ($uid): ?>
<li><?php echo anchor("/remove", "Remove Edits"); ?></li>
<?php endif; ?>
</ul></li>
<?php if ($this->agent->is_browser() and $this->session->userdata('browser') !== "Internet Explorer"): ?>
<li><?php echo anchor("/create", "Edit Creator"); ?></li>
<li><?php echo anchor("#", "View Stepcharts"); ?><ul>
<li><?php echo anchor("/chart", "View Edit Stepcharts"); ?></li>
<li><?php echo anchor("/chart/songs", "View Official Stepcharts"); ?></li>
</ul></li>
<?php endif; ?>
<li><?php echo anchor("/usb", "USB Guide"); ?></li>
<li><?php echo anchor("/contact", "Contact"); ?></li>
<li><?php echo anchor("/thanks", "Credits"); ?></li>
<li><a href="http://www.pumpproedits.com/blog">Blog</a></li>
</ul>
</nav>
