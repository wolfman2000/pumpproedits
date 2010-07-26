<table id="multiCol">
<thead><tr>
<th><?php echo ucfirst($what); ?></th><th>Count</th>
<th><?php echo ucfirst($what); ?></th><th>Count</th>
</tr></thead>
<tbody>
<tr>
<?php $counter = -1;
foreach ($query as $r): ?>
<td><?php echo anchor(sprintf("/%s%d", $what, $r->id), $r->core); ?></td>
<td><?php echo $r->num_edits; ?></td>
<?php if (++$counter % 2): ?>
</tr>
<tr>
<?php endif; endforeach; ?>
</tr>
</tbody>
</table>
