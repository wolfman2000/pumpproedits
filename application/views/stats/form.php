<?php /*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
echo form_open_multipart('stats/process'); ?>
<fieldset><legend>Select your .edit file.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="file">File</label></dt>
<dd><input id="file" type="file" name="file" /></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>
