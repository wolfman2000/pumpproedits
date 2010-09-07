<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
?>
<div class="clear"></div>
<ul id="multiCol" class="grid_12">
<?php foreach ($query as $r): ?>
<li class="grid_6">
<div class="grid_4"><?php echo anchor(sprintf("/%s/%d", $what, $r->id), $r->core); ?></div>
<div class="grid_1 suffix_1"><?php echo $r->num_edits; ?></div>
</li>
<?php endforeach; ?>
</ul>
