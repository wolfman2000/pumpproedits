</article>
<?php if ($this->uri->segment(1) === "create") { $this->load->view("global/nav_create"); }; ?>
<footer>
<p>This website is ©2009-2010 <a href="mailto:jafelds@gmail.com">Jason "Wolfman2000" Felds</a>.<br />
This website works best in <a href="http://www.firefox.com">Firefox</a>.</p>
<?php if (strpos($_SERVER['SERVER_NAME'], "localhost") === false): ?>
<!-- Site Meter -->
<script type="text/javascript" src="http://s36.sitemeter.com/js/counter.js?site=s36pumpproedits">
</script>
<noscript>
<a href="http://s36.sitemeter.com/stats.asp?site=s36pumpproedits" target="_top">
<img src="http://s36.sitemeter.com/meter.asp?site=s36pumpproedits" alt="Site Meter" border="0"/></a>
</noscript>
<!-- Copyright (c)2009 Site Meter -->
<?php endif; ?>
</footer>
</body>
</html>
