<?php echo form_open_multipart('upload/process'); ?>
<fieldset><legend>Select your .edit file.</legend>
<?php echo validation_errors(); ?>
<dl>
<dt><label for="file">File</label></dt>
<dd><input id="file" type="file" name="file" /></dd>
</dl>
<p><input type="hidden" name="userid" id="userid" value="<?php echo $this->session->userdata('id'); ?>" />
<button value="submit" type="submit" id="submit" name="submit">Submit!</button></p>
</fieldset>
</form>