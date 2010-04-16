<?php $this->load->view('global/header',
  array('css' => 'css/stats.css', 'h2' => 'Edit Stats Gotten', 'title' => 'Edits Gotten')); ?>
<p>The stats for your uploaded edit are as follows:</p>
<ul>
<?php foreach($upload_data as $item => $value):?>
<li><?php echo $item;?>: <?php echo $value;?></li>
<?php endforeach; ?>
</ul>

<?php $this->load->view('stats/form');
$this->load->view('global/footer');