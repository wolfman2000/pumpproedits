<table id="edits">
<caption><?php echo html_entity_decode($caption, ENT_COMPAT, "UTF-8") ?></caption>
<?php if (isset($showuser) and isset($showsong)): ?>
<col span="2" />
<?php elseif (isset($showuser) or isset($showsong)): ?>
<col />
<?php endif; ?>
<col />
<col id="statcol" />
<col />
<thead><tr>
<?php if (isset($showuser)): ?><th>User</th><?php endif; ?>
<?php if (isset($showsong)): ?><th>Song</th><?php endif; ?>
<th>Title</th>
<th>Stats</th>
<th>Actions</th>
</tr></thead>
<tbody>
<?php foreach ($query as $z): ?>
<tr>
<?php if (isset($showuser)): ?>
<td><?php
if ($z->user_id == 2):
$route = "/official";
#elseif ($z->user_id == 95):
#$route = "@edit_unknown";
else:
$route = "/user/$z->user_id";
endif;
echo anchor($route, $z->uname); ?></td>
<?php endif;
if (isset($showsong)): ?>
<td><?php echo anchor("/song/$z->song_id", $z->sname); ?></td>
<?php endif; ?>
<td><?php echo $z->title ?></td>
<td>
<dl>
<?php $l = substr(ucfirst($z->style), 0, 1); ?>
<dt>Style</dt><dd><?php echo $l . $z->diff ?></dd>
<dt>Steps</dt><dd><?php echo $z->ysteps . ($l === "R" ? "/$z->msteps" : "") ?></dd>
<?php if ($z->yjumps or $z->mjumps): ?>
<dt>Jumps</dt><dd><?php echo $z->yjumps . ($l === "R" ? "/$z->mjumps" : "") ?></dd>
<?php endif;
if ($z->yholds or $z->mholds): ?>
<dt>Holds</dt><dd><?php echo $z->yholds . ($l === "R" ? "/$z->mholds" : "") ?></dd>
<?php endif;
if ($z->ymines or $z->mmines): ?>
<dt>Mines</dt><dd><?php echo $z->ymines . ($l === "R" ? "/$z->mmines" : "") ?></dd>
<?php endif;
if ($z->ytrips or $z->mtrips): ?>
<dt>Trips</dt><dd><?php echo $z->ytrips . ($l === "R" ? "/$z->mtrips" : "") ?></dd>
<?php endif;
if ($z->yrolls or $z->mrolls): ?>
<dt>Rolls</dt><dd><?php echo $z->yrolls . ($l === "R" ? "/$z->mrolls" : "") ?></dd>
<?php endif;
if ($z->ylifts or $z->mlifts): ?>
<dt>Lifts</dt><dd><?php echo $z->ylifts . ($l === "R" ? "/$z->mlifts" : "") ?></dd>
<?php endif;
if ($z->yfakes or $z->mfakes): ?>
<dt>Fakes</dt><dd><?php echo $z->yfakes . ($l === "R" ? "/$z->mfakes" : "") ?></dd>
<?php endif; ?>
</dl>
</td>
<td><ul>
<li><?php echo anchor("/edits/download/$z->id", "Download"); ?></li>
<?php if ($this->session->userdata('browser') !== "Internet Explorer"): ?>
<li><?php echo anchor("/chart/quick/{$z->id}/classic", "Classic Chart"); ?></li>
<li><?php echo anchor("/chart/quick/{$z->id}/rhythm",  "Rhythm Chart");  ?></li>
<?php endif; ?>
</ul></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
