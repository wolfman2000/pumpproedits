<?php echo form_open('chart/songProcess'); ?>
<fieldset><legend>Select the song and difficulty to preview.</legend>
<?php echo validation_errors(); ?>

<section id="edit">
<p><label for="edits">Choose a song</label></p>
<p><select id="edits" name="edits" size="20">
<?php $oid = "無"; # Start with no match. ?>
<option value="無" selected="selected">Select a song.</option>
<?php foreach ($songs as $r):
$nid = $r->gid;
if ($oid !== $nid):
if ($oid !== "無"): ?>
</optgroup>
<?php endif; ?>
<optgroup label="<?php echo "Pump it up Pro" . ($nid == 1 ? "" : " 2"); ?>">
<?php $oid = $nid;
endif; ?>
<option value="<?php echo $r->id; ?>"><?php echo $r->name; ?></option>
<?php endforeach; ?>
</optgroup>
</select></p>
</section>
<section>
<p><label for="diff">Difficulty</label></p>
<p><select id="diff" name="diff">
<option value="ez">Easy</option>
<option value="nr">Normal</option>
<option value="hr">Hard</option>
<option value="cz" selected="selected">Crazy</option>
<option value="hd">Halfdouble</option>
<option value="fs">Freestyle</option>
<option value="nm">Nightmare</option>
<option value="rt">Routine</option>
</select></p>
<p><label for="kind">Noteskin</label></p>
<p><select id="kind" name="kind">
<option selected="selected" value="classic">Classic</option>
<option value="rhythm">Rhythm</option>
</select></p>
<p><label for="red4">4th Note Color</label></p>
<p><select id="red4" name="red4">
<option value="0" selected="selected">Blue</option>
<option value="1">Red</option>
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
