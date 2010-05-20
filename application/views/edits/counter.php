<section id="multiCol">
<?php foreach ($query as $r): ?>
<p><?php echo anchor(sprintf("/%s/%d", $what, $r->id), $r->core); ?>
<span><?php echo $r->num_edits; ?></span></p>
<?php endforeach; ?>
</section>
