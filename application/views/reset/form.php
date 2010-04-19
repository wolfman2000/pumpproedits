<?php echo form_open('reset/check'); ?>
<fieldset><legend>Fill in all of the fields.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="confirm">Confirmation Code</label></dt>
<dd><input type="text" name="confirm" id="confirm" maxlength="32" value="<?php echo $this->uri->segment(3, FALSE); ?>" /></dd>
<dt><label for="password">Password</label></dt>
<dd><input id="password" type="password" name="password" /></dd>
<dt><label for="passdual">Confirm Password</label></dt>
<dd><input id="passdual" type="password" name="passdual" /></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>