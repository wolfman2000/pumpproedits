<?php echo form_open('chart/songProcess'); ?>
<fieldset><legend>Select the song and difficulty to preview.</legend>
<div class="clear"></div>
<section id="errorCatch" class="grid_6">
<?php echo validation_errors(); ?>
</section>
<div class="clear"></div>
<div class="prefix_1 grid_10">

<section id="song" class="grid_5 alpha">
<p><label for="songs">Choose a song</label></p>
<p><select id="songs" name="songs" size="20">
<?php $oid = "無"; /* Start with no match. */ ?>
<option value="無" selected="selected">Select a song.</option>
<?php foreach ($songs as $r):
$nid = $r['gid'];
if ($oid !== $nid):
if ($oid !== "無"): ?>
</optgroup>
<?php endif; ?>
<optgroup label="<?php echo $r['game']; ?>">
<?php $oid = $nid;
endif; ?>
<option value="<?php echo $r['id']; ?>"><?php echo $r['name']; ?></option>
<?php endforeach; ?>
</optgroup>
</select></p>
</section>
<?php $options = array
(
  array
  (
    "for" => "diff", "label" => "Difficulty", "choices" => array
    (
      array("value" => "ez", "text" => "Easy", "selected" => false),
      array("value" => "nr", "text" => "Normal", "selected" => false),
      array("value" => "hr", "text" => "Hard", "selected" => false),
      array("value" => "cz", "text" => "Crazy", "selected" => true),
      array("value" => "hd", "text" => "Halfdouble", "selected" => false),
      array("value" => "fs", "text" => "Freestyle", "selected" => false),
      array("value" => "nm", "text" => "Nightmare", "selected" => false),
      array("value" => "rt", "text" => "Routine", "selected" => false),
    ),
  ),
  array
  (
    "for" => "kind", "label" => "Note Style", "choices" => array
    (
      array("value" => "classic", "text" => "Classic", "selected" => true),
      array("value" => "rhythm", "text" => "Rhythm", "selected" => false),
      array("value" => "flat", "text" => "Flat", "selected" => false),
    ),
  ),
  array
  (
    "for" => "red4", "label" => "4th Note Color", "choices" => array
    (
      array("value" => "blue", "text" => "Blue", "selected" => true),
      array("value" => "red", "text" => "Red", "selected" => false),
      array("value" => "smiley", "text" => "Smiley", "selected" => false),
    ),
  ),
  array
  (
  	"for" => "noteskin", "label" => "Noteskin", "choices" => array
  	(
  	  array("value" => "original", "text" => "Original", "selected" => true),
  	  array("value" => "stepmania", "text" => "StepMania", "selected" => false),
  	  array("value" => "smiley", "text" => "Smiley", "selected" => false),
	),
  ),
  array
  (
    "for" => "speed", "label" => "Speed Mod", "choices" => array
    (
      array("value" => 1, "text" => 1, "selected" => false),
      array("value" => 2, "text" => 2, "selected" => true),
      array("value" => 3, "text" => 3, "selected" => false),
      array("value" => 4, "text" => 4, "selected" => false),
      array("value" => 6, "text" => 6, "selected" => false),
      array("value" => 8, "text" => 8, "selected" => false),
    ),
  ),
  array
  (
    "for" => "mpcol", "label" => "Measures per column", "choices" => array
    (
      array("value" => 4, "text" => 4, "selected" => false),
      array("value" => 6, "text" => 6, "selected" => true),
      array("value" => 8, "text" => 8, "selected" => false),
      array("value" => 12, "text" => 12, "selected" => false),
      array("value" => 16, "text" => 16, "selected" => false),
    ),
  ),
  array
  (
    "for" => "scale", "label" => "Scale Factor", "choices" => array
    (
      array("value" => 0.5, "text" => 0.5, "selected" => false),
      array("value" => 0.75, "text" => 0.75, "selected" => false),
      array("value" => 1, "text" => 1, "selected" => true),
      array("value" => 1.25, "text" => 1.25, "selected" => false),
      array("value" => 1.5, "text" => 1.5, "selected" => false),
      array("value" => 1.75, "text" => 1.75, "selected" => false),
      array("value" => 2, "text" => 2, "selected" => false),
    ),
  ),
); ?>

<section id="options" class="grid_5 omega">
<?php foreach ($options as $option): ?>
<div class="grid_3 alpha"><label for="<?php echo $option["for"]; ?>"><?php echo $option['label']; ?></label></div>
<div class="grid_2 omega pushDown"><select id="<?php echo $option["for"]; ?>" name="<?php echo $option["for"]; ?>">
<?php foreach ($option['choices'] as $choice): ?>
<option <?php if ($choice['selected']) { ?>selected="selected" <?php } ?>value="<?php echo $choice['value']; ?>"><?php echo $choice['text']; ?></option>
<?php endforeach; ?>
</select></div>
<div class="reset"></div>
<?php endforeach; ?>
</section>

</div>
<div class="clear"></div>
<p><button id="submit" name="submit" type="submit" value="submit">Submit!</button></p>


</fieldset>
</form>

