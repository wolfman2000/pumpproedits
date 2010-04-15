<p>Welcome <?php echo $this->session->userdata('name'); ?>.</p>
<p><?php echo anchor("/logout", "Log Out"); ?> when finished.</p>