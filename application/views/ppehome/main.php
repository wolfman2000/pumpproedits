<?php $this->load->view('global/header', array('css' => 'css/main.css')); ?>
<p>Welcome to the Pump Pro Edit database. Inside here, you will find
many edits that dance players such as yourself have created, along
with official charts that Andamiro and Fun In Motion staff made themselves.</p>
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
<p>To whet your appetite for edits, here are five of them chosen at random.</p>
<?php $this->load->library('pagination'); # Dummy for now.
$this->load->view('edits/edits');
$this->load->view('global/footer');
