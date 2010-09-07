<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
echo form_open('confirm/check'); ?>
<fieldset><legend>Fill in all of the fields.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="confirm">Confirmation Code</label></dt>
<dd><input type="text" name="confirm" id="confirm" maxlength="32" value="<?php echo $this->uri->segment(3, FALSE); ?>" /></dd>
<dt><label for="password">Password</label></dt>
<dd><input id="password" type="password" name="password" /></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>
