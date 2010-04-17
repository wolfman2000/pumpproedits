<?php $this->load->view('global/header',
  array('css' => 'css/stats.css', 'h2' => 'Edit Stats Gotten', 'title' => 'Edit Stats Gotten'));
$style = $result['style'];
function statRow($dt, $dd, $style)
{
  echo "<dt>$dt</dt><dd>${dd[0]}";
  if ($style === "pump-routine") { echo "/${dd[1]}"; }
  echo "</dd>\r\n";
}?>
<p>Your edit was parsed successfully. Its stats are as follows:</p>

<dl>
<dt>Title</dt><dd><?php echo $result['title'] ?></dd>
<dt>Style</dt><dd><?php echo $style ?></dd>
<dt>Difficulty</dt><dd><?php echo $result['diff'] ?></dd>
<?php statRow("Steps", $result['steps'], $style);
statRow("Jumps", $result['jumps'], $style);
statRow("Holds", $result['holds'], $style);
statRow("Mines", $result['mines'], $style);
statRow("Trips", $result['trips'], $style);
statRow("Rolls", $result['rolls'], $style); ?>
</dl>

<p>If you wish to get stats for another file, you can do so below.</p>
<?php $this->load->view('stats/form');
$this->load->view('global/footer');