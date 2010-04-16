<section id="multiCol">
<?php foreach ($query as $r): ?>
<p><span><?php echo anchor(sprintf("/%s/%d", $what, $r->id), $r->core); ?></span>
<?php echo $r->num_edits; ?></p>
<?php endforeach; ?>
</section>