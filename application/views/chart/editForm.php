<?php echo form_open('chart/editProcess'); ?>
<fieldset><legend>Select the edit to preview.</legend>
<?php echo validation_errors(); ?>

<section id="edit">
<p><label for="edits">Choose an edit</label></p>
<p><select id="edits" name="edits" size="20">
<?php $oname = "無"; # Start with no match. ?>
<option value="無" selected="selected">Select an edit.</option>
<?php foreach ($edits as $r):
$nname = $r['uname'];
if ($oname !== $nname):
if ($oname !== "無"): ?>
</optgroup>
<?php endif; ?>
<optgroup label="<?php echo $nname; ?>">
<?php $oname = $nname;
endif; ?>
<option value="<?php echo $r['id']; ?>"><?php echo sprintf("%s → %s (%s%d)",
  $r['sname'], $r['title'], ucfirst(substr($r['style'], 0, 1)), $r['diff']); ?></option>
<?php endforeach; ?>
</optgroup>
</select></p>
</section>
<section>
<p><label for="kind">Note Style</label></p>
<p><select id="kind" name="kind">
<option value="normal">Normal</option>
<option value="flat">Flat</option>
</select></p>
<p><label for="red4">4th Note Color</label></p>
<p><select id="red4" name="red4">
<option value="red" selected="selected">Red</option>
<option value="blue">Blue</option>
</select></p>
<p><label for="noteskin">Note Skin</label></p>
<p><select id="noteskin" name="noteskin">
<option value="original" selected="selected">Original</option>
<option value="smiley">Smiley</option>
<option value="stepmania">StepMania</option>
</select></p>
<p><label for="speed">Speed Mod</label></p>
<p><select id="speed" name="speed">
<option value="1">1</option>
<option value="2" selected="selected">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="6">6</option>
<option value="8">8</option>
</select></p>
<p><label for="mpcol">Measure per column</label></p>
<p><select id="mpcol" name="mpcol">
<option value="4">4</option>
<option value="6" selected="selected">6</option>
<option value="8">8</option>
<option value="12">12</option>
<option value="16">16</option>
</select></p>
<p><label for="scale">Scale Factor</label></p>
<p><select id="scale" name="scale">
<option value="0.5">0.5</option>
<option value="0.75">0.75</option>
<option value="1" selected="selected">1</option>
<option value="1.25">1.25</option>
<option value="1.5">1.5</option>
<option value="1.75">1.75</option>
<option value="2">2</option>
</select></p>
</section>
<p><button id="submit" name="submit" type="submit" value="submit">Submit!</button></p>

</fieldset>
</form>