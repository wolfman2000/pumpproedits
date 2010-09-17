<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/ ?>
<p>The registration was unsuccessful. The issues are below:</p>
<ul>
<?php foreach ($errors as $e): ?>
<li class="error_list"><?php echo $e; ?></li>
<?php endforeach; ?>
</ul>
<p>Please fix these errors and try again.</p>
<?php $this->load->view('register/form');
