<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
echo form_open('contact/mail'); ?>
<fieldset>
<legend class="grid_4">Fill in all of the fields.</legend>
<div class="clear"></div>
<section id="errorCatch" class="grid_6">
<?php echo validation_errors(); ?>
</section>
<div class="clear"></div>
<div class="grid_9">
<div class="grid_3 alpha"><label for="name">Name</label></div>
<div class="grid_6 omega"><input id="name" name="name" type="text" /></div>
</div>
<div class="clear"></div>
<div class="grid_9">
<div class="grid_3 alpha"><label for="email">Email</label></div>
<div class="grid_6 omega"><input id="email" name="email" type="text" maxlength="320" /></div>
</div>
<div class="clear"></div>
<div class="grid_9">
<div class="grid_3 alpha"><label for="subject">Subject</label></div>
<div class="grid_6 omega"><input id="subject" name="subject" type="text" maxlength="50" /></div>
</div>
<div class="clear"></div>
<div class="grid_9">
<div class="grid_3 alpha"><label for="content">Content</label></div>
<div class="grid_6 omega"><textarea id="content" name="content" cols="30" rows="4"></textarea></div>
</div>
<div class="clear"></div>
<p><button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>
