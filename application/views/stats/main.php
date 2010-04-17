<?php $this->load->view('global/header',
  array('css' => 'css/stats.css', 'h2' => 'Get Edit\'s Stats', 'title' => 'Edit Stat Getter')); ?>
<p>If you are unable to use the Edit Charter to see the
stats of your edit, use the form below. Make sure it follows
the general format below.</p>
<pre>
#SONG:<var>Song Name</var>;
#NOTES:
dance-<var>single OR double</var>:
<var>EditNameHere</var>:
Edit:
<var>1-99</var>:
<var>Comma separated list of 5+ numbers on a single line</var>:

0010
0000
0000
0000
,
1001
0000
0110
0000
;
</pre>
<?php $this->load->view('stats/form');
$this->load->view('global/footer');