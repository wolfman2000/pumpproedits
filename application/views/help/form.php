<?php echo form_open('help/check'); ?>
<fieldset><legend>Fill in all of the fields.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="email">Email</label></dt>
<dd><input type="email" name="email" id="email" maxlength="320" /></dd>
<dt><label for="choice">Choice</label></dt>
<dd><select id="choice" name="choice">
<option value="reset">Reset my password.</option>
<option value="resend">Resend my confirmation email.</option>
</select></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>
