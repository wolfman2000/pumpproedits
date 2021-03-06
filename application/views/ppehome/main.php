<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/ ?>
<p>Welcome to the Pump Pro Edit database. Inside here, you will find
many edits that dance players such as yourself have created, along
with official charts that Andamiro and Fun In Motion staff made themselves.</p>
<?php if (!$modern): ?>
<p>To actually view the charts in a graphical format or contribute
edit content to the website, you will have to use a different web
browser. The recommended browsers are listed at the top of this page.</p>
<?php elseif ($browser == "ie"): ?>
<p>In terms of special features, you are able to view the charts in the
graphical SVG format. In order to properly use the Edit Creator, however,
it is recommended to use a browser that is listed at the top of this page.</p>
<?php else: ?>
<p>Want to make an edit yourself? There is a new
<?php echo anchor("/create", "Edit Creator"); ?> tool
available for you to use. As an added bonus, if you log in before visiting,
you will be able to upload your work directly to your account!</p>
<?php endif; ?>
<p>To whet your appetite for edits, here are five of them chosen at random.</p>
<?php $this->load->library('pagination'); # Dummy for now.
$this->load->view('edits/edits');

