<p>Welcome <?php echo $this->session->userdata('username'); ?>.</p>
<p><?php echo anchor("/logout", "Log Out"); ?> when finished.</p>