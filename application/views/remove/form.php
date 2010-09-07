<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
echo form_open_multipart('remove/process'); ?>
<fieldset><legend>Select the .edit files to remove.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt>Edits</dt>
<dd><ul>
<?php foreach ($edits as $e): ?>
<li><input type="checkbox" name="removing[]" value="<?php echo $e['id']; ?>" id="edit_<?php echo $e['id']; ?>" />
<label for="edit_<?php echo $e['id']; ?>"><?php echo $e['sname']; ?> →
<?php echo $e['title']; ?> (<?php echo ucfirst(substr($e['style'], 0, 1)) . $e['diff']; ?>)</label></li>
<?php endforeach; ?>
</ul></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>
