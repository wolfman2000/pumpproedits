<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
?>
<nav id="main_nav" class="grid_12">
<ul class="sf-menu">
<li><?php echo anchor("/", "Home"); ?></li>
<li><?php echo anchor("#", "Browse Edits"); ?><ul>
<li><?php echo anchor("/songs", "List by Song"); ?></li>
<li><?php echo anchor("/users", "List by Author"); ?></li>
<li><?php echo anchor("#", "Official Edits"); ?><ul>
<li><?php echo anchor("/arcade", "Arcade Mode Edits"); ?></li>
<li><?php echo anchor("/another", "Another Edits"); ?></li>
<li><?php echo anchor("/mission", "Mission Mode Edits"); ?></li>
<li><?php echo anchor("/gauntlet", "Gauntlet Edits"); ?></li>
</ul></li>
<?php if ($this->session->userdata('id')): ?>
<li><?php echo anchor("/remove", "Remove Edits"); ?></li>
<?php endif; ?>
</ul></li>
<?php if ($browser != "ie"): ?>
<li><?php echo anchor("/create", "Edit Creator"); ?></li>
<?php endif;
if ($modern): ?>
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
<article class="grid_12">
<h2><?php echo $h2; ?></h2>
<div id="bd">
