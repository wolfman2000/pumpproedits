</div></article>
<?php if ($this->uri->segment(1) === "create") { $this->load->view("global/nav_create"); }; ?>
<div class="clear"></div>
<div id="loginbox" class="hide" title="Log in">
	<p>Please log in below. If you do not have an account, feel free to register.
  If you forgot your password, you can still recover your account.</p>
  <?php echo form_open('login/check', array('id' => 'loginForm')); ?>
  <fieldset><?php #<legend>Fill in all of the fields.</legend> ?>
  <dl>
  <dt><label for="username">Username</label></dt>
  <dd><input type="text" name="username" id="username" /></dd>
  <dt><label for="password">Password</label></dt>
  <dd><input id="password" type="password" name="password" /></dd>
  </dl>
  </fieldset>
  </form>
</div>

<footer class="grid_12">
<section class="grid_9" id="footer-left">
<p>Sister Site: <a href="http://www.itgedits.info/" title="ITG Edits">ITG Edits</a><br />
<a href="http://www.twitter.com/pumpproedits" title="Twitter">Follow PumpProEdits on Twitter</a>
</p>
</section>
<section class="grid_3" id="footer-right">
<p>Proudly built by <a href="mailto:jafelds@gmail.com">Jason "Wolfman2000" Felds</a></p>
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
