<?php echo form_open('register/check'); ?>
<fieldset><legend>Fill in all of the fields.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="username">Username</label></dt>
<dd><input type="text" name="username" id="username" maxlength="12" /></dd>
<dt><label for="password">Password</label></dt>
<dd><input id="password" type="password" name="password" /></dd>
<dt><label for="passdual">Confirm Password</label></dt>
<dd><input id="passdual" type="password" name="passdual" /></dd>
<dt><label for="password">Email</label></dt>
<dd><input id="email" type="email" name="email" maxlength="320" /></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>