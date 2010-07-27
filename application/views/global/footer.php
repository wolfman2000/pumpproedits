</div></article>
<?php if ($this->uri->segment(1) === "create") { $this->load->view("global/nav_create"); }; ?>
<div class="clear"></div>
<footer class="grid_12">
<section class="grid_9" id="footer-left">
<p>Questions? Comments? Bugs? <a href="http://www.pumpproedits.com/contact" title="Contact">Contact me</a><br />
Please also check out our sister site, <a href="http://www.itgedits.info/" title="ITG Edits">ITG Edits</a><br />
<a href="http://www.twitter.com/pumpproedits" title="Twitter">Follow me on Twitter</a>
</p>
</section>
<section class="grid_3" id="footer-right">
<p>Proudly built by Jason "Wolfman2000" Felds</p>
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
</section>
</footer>
</body>
</html>
