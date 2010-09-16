<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
?>
<nav id="main_nav" class="grid_12">
<?php $uid = $this->session->userdata('id');

# $this->load->view('global/mess_' . ($uid === false ? 'out' : 'in')); ?>
<ul class="sf-menu">
<li><?php echo anchor("/", "Home"); ?></li>
<li><?php echo anchor("#", "Browse Edits"); ?><ul>
<li><?php echo anchor("/songs", "List by Song"); ?></li>
<li><?php echo anchor("/users", "List by Author"); ?></li>
<li><?php echo anchor("/official", "List Official Edits"); ?></li>
<?php if ($uid): ?>
<li><?php echo anchor("/remove", "Remove Edits"); ?></li>
<?php endif; ?>
</ul></li>
<?php if (browser_detection('browser_working') != "ie"): ?>
<li><?php echo anchor("/create", "Edit Creator"); ?></li>
<?php endif;
if (browser_detection('browser_working') != "ie" or browser_detection("ie_version") == "ie9x"): ?>
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
