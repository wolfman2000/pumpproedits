<?php $this->load->view('global/header', array('css' => 'css/main.css')); ?>
<p>Welcome to the Pump Pro Edit database. Inside here, you will find
many edits that dance players such as yourself have created, along
with official charts that Pump creator Andamiro made themselves.</p>
<?php if ($this->agent->is_browser() and $this->session->userdata('browser') === "Internet Explorer"): ?>
<p>To actually view the charts in a graphical format or contribute
edit content to the website, you will have to use a different web
browser. The recommended browsers are listed at the top of this page.
<?php else: ?>
<p>Want to make an edit yourself? There is a new
<?php echo anchor("/create", "Edit Creator"); ?> tool
available for you to use. As an added bonus, if you log in before visiting,
you will be able to upload your work directly to your account!</p>
<?php endif; ?>
<p>If you want instant access on when edits are contributed or
when there is an internal website update,
<a href="http://www.twitter.com/pumpproedits">follow pumpproedits
on Twitter</a>.</p>
<p>Hope you enjoy the place and the new look!</p>
<?php $this->load->view('global/footer'); ?>
