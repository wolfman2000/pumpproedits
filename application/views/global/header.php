<?php if (!(isset($xhtml))) { $xhtml = ''; } echo $xhtml; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<!--
PHP/HTML file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
-->
<meta charset="UTF-8" />
<meta name="title" content="Pump Pro Edits" />
<meta name="description" content="This website allows users to create and share edits for the game Pump It Up Pro." />
<meta name="keywords" content="Pump It Up Pro, Pump Pro, Pump It Up, Pro, edits, Wolfman2000, Jason Felds" />
<meta name="lang" content="en" />
<meta name="robots" content="index, follow" />
<title><?php echo $title; ?></title><link rel="shortcut icon" href="/favicon.ico" />
<?php echo link_tag($css);
if ($browser == "ie"): ?>
<script type="text/javascript" src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<script type="text/javascript" src="js/ie_html5.js"></script>
<?php endif; # Info below is for various pages. ?>
<script type="text/javascript">
//<![CDATA[
<?php $uid = $this->session->userdata('id'); ?>
<?php if (isset($baseEdits)): 
$maxPages = floor($baseEdits / APP_BASE_EDITS_PER_PAGE);
if ($baseEdits % APP_BASE_EDITS_PER_PAGE) { $maxPages++; } ?>
const maxPages = <?php echo $maxPages; ?>;
<?php endif; if (isset($maxEdits)):
$maxPages = floor($maxEdits / APP_MAX_EDITS_PER_PAGE);
if ($maxEdits % APP_MAX_EDITS_PER_PAGE) { $maxPages++; } ?>
const maxPages = <?php echo $maxPages; ?>;
<?php endif; if (isset($const_user)):?>
const userID = <?php echo $const_user; ?>;
<?php endif; if (isset($const_song)):?>
const songID = <?php echo $const_song; ?>;
<?php endif; ?>
//]]>
</script>
<?php foreach ($scripts as $script): ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php endforeach; ?>
</head>
<body class="container_12">

<header>
  <h1 class="grid_7" id="logo"><a href="/">
    <img src="/images/logo.png" width="392px" height="80px" alt="Pump Pro Edits" title="Pump Pro Edits" />
  </a></h1>
  <div class="grid_5 alpha" id="userbar">
    <ul>
      <?php if (!$modern): ?>
      <li><a href="http://www.firefox.com">Firefox</a></li>
      <li><a href="http://chrome.google.com">Chrome</a></li>
      <li><a href="http://www.apple.com/safari">Safari</a></li>
      <li><a href="http://www.opera.com">Opera</a></li>
      <?php elseif ($this->session->userdata('id')): # logged in ?>
      <li><a href="#"><?php echo $this->session->userdata('username'); ?></a></li>
      <li><?php echo anchor("/user/" . $this->session->userdata('id'), "Your Edits"); ?></li>
      <li><?php echo anchor("/logout", "Log out"); ?></li>
      <?php else: ?>
      <li><?php echo anchor("/register", "Register"); ?></li>
      <li class="hide"><a href="#" id="loginlink">Log in</a></li>
      <?php endif; ?>
    </ul>
    <?php $logStat = $this->session->flashdata('loginResult'); if ($logStat): 
    if (strpos($logStat, "Welcome") !== false): /* ?>
    <div class="ui-widget">
			<div class="ui-state-highlight ui-corner-all"> 
				<p><?php echo $logStat; ?></p>
			</div>
		</div>
    <?php */ else: # Did not log in. ?>
    <div class="ui-widget">
			<div class="ui-state-error ui-corner-all"> 
				<p><?php # <span class="ui-icon ui-icon-alert"></span> ?>
				<strong>Alert:</strong> <?php echo $logStat; ?></p>
			</div>
		</div>
    <?php endif; # end login message ?>
    <?php endif; # end check for login attempt ?>
  </div>
</header>
<div class="clear"></div>
