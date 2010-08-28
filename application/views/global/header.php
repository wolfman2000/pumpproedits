<?php if (!(isset($xhtml))) { $xhtml = ''; } echo $xhtml; ?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta charset="UTF-8" />
<meta name="title" content="Pump Pro Edits" />
<meta name="description" content="This website allows users to create and share edits for the game Pump It Up Pro." />
<meta name="keywords" content="Pump It Up Pro, Pump Pro, Pump It Up, Pro, edits, Wolfman2000, Jason Felds" />
<meta name="lang" content="en" />
<meta name="robots" content="index, follow" />
<title><?php if (isset($title)):
$title .= " — Pump Pro Edits";
else: $title = "Pump Pro Edits"; endif; echo $title; ?></title><link rel="shortcut icon" href="/favicon.ico" />
<?php if (!(isset($css))) { $css = 'css/main.css'; } 
$allCSS = array("css/960/960.css", "css/960/reset.css", "css/960/text.css",
"css/superfish.css", "css/custom-theme/jquery-ui-1.8.2.custom.css", $css);
foreach ($allCSS as $ac) { echo link_tag($ac); }
if ($this->session->userdata('browser') === false) { $this->session->set_userdata('browser', $this->agent->browser()); }
if ($this->agent->is_browser() and $this->session->userdata('browser') === "Internet Explorer"): ?>
<script type="text/javascript" src="http://ie7-js.googlecode.com/svn/version/2.1(beta4)/IE9.js"></script>
<script type="text/javascript" src="js/ie_html5.js"></script>
<?php endif; # Info below is for various pages. ?>
<script type="text/javascript">
//<![CDATA[
<?php $uid = $this->session->userdata('id'); ?>
const authed = <?php echo $uid === false ? 0 : $uid; ?>;
const baseURL = window.location.href;
<?php if (strlen($xhtml)): ?>
const andamiro = <?php echo $uid === false ? 0 : $andy; ?>;
const others = <?php echo $uid === false ? 0 : $others; ?>;
<?php endif; ?>
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
<?php $baseScripts = array("/js/jquery-1.4.2.js", "/js/jquery-ui-1.8.2.custom.min.js",
"/js/hoverIntent.js", "/js/superfish.js", "/js/supersubs.js", "/js/allPages.js");
if (isset($scripts)) { $baseScripts = array_merge($baseScripts, $scripts); }
foreach ($baseScripts as $script): ?>
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
      <?php if ($this->agent->is_browser() and $this->session->userdata('browser') === "Internet Explorer"): ?>
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
<?php $this->load->view('global/nav_normal'); ?>
<article class="grid_12">
<h2><?php if (!(isset($h2))) { $h2 = "Welcome!"; } echo $h2; ?></h2>
<div id="bd">
