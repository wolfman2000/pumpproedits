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
<link rel="stylesheet" type="text/css" media="all" href="/css/reset.css" />
<link rel="stylesheet" type="text/css" media="all" href="/css/text.css" />
<link rel="stylesheet" type="text/css" media="all" href="/style.css" />
<link rel="stylesheet" type="text/css" href="/css/superfish.css" media="screen" />
<?php if (!(isset($css))) { $css = 'css/main.css'; } echo link_tag($css);
if ($this->session->userdata('browser') === false) { $this->session->set_userdata('browser', $this->agent->browser()); }
if ($this->agent->is_browser() and $this->session->userdata('browser') === "Internet Explorer"): ?>
<script type="text/javascript" src="js/IE8.js"></script>
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
<script type="text/javascript" src="/js/jquery-1.4.2.js"></script>
<script type="text/javascript" src="/js/jquery-ui-1.8.2.custom.min.js"></script>
<script type="text/javascript" src="/js/hoverIntent.js"></script>
<script type="text/javascript" src="/js/superfish.js"></script>
<script type="text/javascript" src="/js/supersubs.js"></script>
<script type="text/javascript" src="/js/allPages.js"></script>
<?php if (isset($scripts)): foreach ($scripts as $script): ?>
<script type="text/javascript" src="<?php echo $script; ?>"></script>
<?php endforeach; endif; ?>
</head>
<body>

<div id="loginbox" title="Log in">
	Lorem ipsum dolor sit amet, consectetuer adipiscing elit, sed diam nonummy nibh euismod tincidunt ut laoreet dolore magna aliquam erat volutpat. Ut wisi enim ad minim veniam
</div>
<header>
  <h1><?php echo anchor("/", "Pump Pro Edits"); ?></h1>
  <div id="userbar">
    <ul>
      <li><?php echo anchor("/register", "Register"); ?></li>
      <li><?php echo anchor("/login", "Log in", array("id" => "loginlink")); ?></li>
    </ul>
  </div>
</header>
<nav id="main_nav">


<?php $uid = $this->session->userdata('id');

# $this->load->view('global/mess_' . ($uid === false ? 'out' : 'in')); ?>
<ul class="sf-menu">
<li><?php echo anchor("/", "Home"); ?></li>
<li><?php echo anchor("#", "Account Stuff"); ?></li>
<li><?php echo anchor("#", "Edits"); ?><ul>
<li><?php echo anchor("/songs", "List by Song"); ?></li>
<li><?php echo anchor("/users", "List by Author"); ?></li>
<li><?php echo anchor("/official", "List Official Edits"); ?></li>
<li><?php echo anchor("/create", "Edit Creator"); ?></li>
</ul></li>
<li><?php echo anchor("#", "Stepcharts"); ?><ul>
<li><?php echo anchor("/chart", "View Edit Stepcharts"); ?></li>
<li><?php echo anchor("/chart/songs", "View Official Stepcharts"); ?></li>
</ul></li>
<li><?php echo anchor("/usb", "USB Usage"); ?></li>
<li><?php echo anchor("/contact", "Contact"); ?></li>
<li><?php echo anchor("/thanks", "Credits"); ?></li>
<li><a href="http://www.pumpproedits.com/blog">Blog</a></li>

</ul>
</nav>

<!--
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
-->

<article>
<h2><?php if (!(isset($h2))) { $h2 = "Welcome!"; } echo $h2; ?></h2>
