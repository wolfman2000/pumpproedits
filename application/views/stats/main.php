<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
$this->load->view('global/header',
  array('css' => 'css/stats.css', 'h2' => 'Get Edit\'s Stats', 'title' => 'Edit Stat Getter')); ?>
<p>If you are unable to use the Edit Charter to see the
stats of your edit, use the form below. Make sure it follows
the general format below.</p>
<pre>
#SONG:<var>Song Name</var>;
#NOTES:
pump-<var>single, double, halfdouble, OR routine</var>:
<var>EditNameHere</var>:
Edit:
<var>1-99</var>:
<var>Comma separated list of 5+ numbers on a single line</var>:

00100
00000
00000
00000
,
10101
00000
01110
00000
;
</pre>
<?php $this->load->view('stats/form');
$this->load->view('global/footer');
