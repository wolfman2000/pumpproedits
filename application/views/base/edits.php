<?php $style = array('single', 'double', 'halfdouble', 'routine'); ?>
<?php echo $this->pagination->create_links(); ?>
<table id="base">
  <caption>Download the Base Edit Files</caption>
  <thead><tr>
    <th>Song Name</th>
    <?php foreach ($style as $st): ?>
    <th>pump-<?php echo $st; ?></th>
    <?php endforeach; ?>
  </tr></thead>
  <tbody>
    <?php foreach ($edits as $b): ?>
    <tr>
      <td><?php echo $b->name ?></td>
      <?php $s = "/base/download/%d/%s"; ?>
      <?php foreach ($style as $st):
      $url = sprintf($s, $b->id, $st);
      $txt = $b->abbr . " " . ucfirst($st); ?>
      <td><?php if ($st !== "routine" or $b->tmp) echo anchor($url, $txt); ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php echo $this->pagination->create_links(); ?>