<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/contact.css', 'h2' => 'Contact the Webmaster', 'title' => 'Contact the Webmaster')); ?>
<p>If you need to reach the web master for any reason, you have
two easy ways of doing so. Those familiar with sending email
through client programs such as Apple's Mail or Mozilla Thunderbird
can <a href="mailto:jafelds@gmail.com">email me directly.</a> A
form alternative is also provided below. Use whichever is convenient
for you.</p>
<?php $this->load->view('contact/form');
$this->load->view('global/footer');
