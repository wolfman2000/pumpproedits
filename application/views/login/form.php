<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
echo form_open('login/check'); ?>
<fieldset><legend>Fill in all of the fields.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="username">Username</label></dt>
<dd><input type="text" name="username" id="username" /></dd>
<dt><label for="password">Password</label></dt>
<dd><input id="password" type="password" name="password" /></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>
