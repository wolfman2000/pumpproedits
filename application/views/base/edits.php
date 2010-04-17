<?php $style = array('single', 'double'); ?>
<table id="base">
  <caption>Download the Base Edit Files</caption>
  <thead><tr>
    <th>Song Name</th>
    <?php foreach ($style as $st): ?>
    <th>dance-<?php echo $st; ?></th>
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
      <td><?php echo anchor($url, $txt); ?></td>
      <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
  </tbody>
</table>
<?php echo $this->pagination->create_links(); ?>