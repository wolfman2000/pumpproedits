<?php echo form_open('contact/mail'); ?>
<fieldset><legend>Fill in all of the fields.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="name">Name</label></dt>
<dd><input id="name" name="name" type="text" /></dd>
<dt><label for="email">Email</label></dt>
<dd><input id="email" name="email" type="text" maxlength="320" /></dd>
<dt><label for="subject">Subject</label></dt>
<dd><input id="subject" name="subject" type="text" maxlength="50" /></dd><dt>
<label for="content">Content</label></dt>
<dd><textarea id="content" name="content" cols="30" rows="4"></textarea></dd>
</dl>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>