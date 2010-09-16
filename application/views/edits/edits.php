<?php 
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
echo $this->pagination->create_links();
$browser = browser_detection('browser_working');
$ie = ($browser === "ie" ? browser_detection('ie_version') : "ie9x");
foreach ($query as $z): ?>
<div class="edit <?php echo $z->style; ?>-style">
  <div class="edit-left">
    <div class="edit-information">
      <span class="edit-title"><?php echo anchor("/edits/download/$z->id", $z->title); ?></span>
      <?php if ($z->first_game_id <= 2 and $z->last_game_id >= 3 and
      	  ($z->style == "routine" or $z->yfakes > 0)): ?>
      <span class="edit_title"><?php echo anchor("/edits/download/$z->id/pro1", "(Pro 1 version)"); ?>
      <?php endif; ?><br />
      <?php if (isset($showuser)): 
        if ($z->user_id == 2):
          $route = "/official";
        else:
          $route = "/user/$z->user_id";
        endif;
        echo anchor($route, $z->uname, array('class' => 'edit-author'));
      endif;
      if (isset($showuser) and isset($showsong)):
        ?>/<?php
      endif;
      if (isset($showsong)):
        echo anchor("/song/$z->song_id", $z->sname, array('class' => 'edit-song'));
      endif; ?>
    </div>
    <div class="edit-stats">
      <dl class="edit-statistics">
        <?php $l = substr(ucfirst($z->style), 0, 1); ?>
        <dt>Steps</dt><dd><?php echo $z->ysteps . ($l === "R" ? "/$z->msteps" : "") ?></dd>
        <?php if ($z->yjumps or $z->mjumps): ?>
        <dt>Jumps</dt><dd><?php echo $z->yjumps . ($l === "R" ? "/$z->mjumps" : "") ?></dd>
        <?php endif;
        if ($z->yholds or $z->mholds): ?>
        <dt>Holds</dt><dd><?php echo $z->yholds . ($l === "R" ? "/$z->mholds" : "") ?></dd>
        <?php endif;
        if ($z->ymines or $z->mmines): ?>
        <dt>Mines</dt><dd><?php echo $z->ymines . ($l === "R" ? "/$z->mmines" : "") ?></dd>
        <?php endif;
        if ($z->ytrips or $z->mtrips): ?>
        <dt>Trips</dt><dd><?php echo $z->ytrips . ($l === "R" ? "/$z->mtrips" : "") ?></dd>
        <?php endif;
        if ($z->yrolls or $z->mrolls): ?>
        <dt>Rolls</dt><dd><?php echo $z->yrolls . ($l === "R" ? "/$z->mrolls" : "") ?></dd>
        <?php endif;
        if ($z->ylifts or $z->mlifts): ?>
        <dt>Lifts</dt><dd><?php echo $z->ylifts . ($l === "R" ? "/$z->mlifts" : "") ?></dd>
        <?php endif;
        if ($z->yfakes or $z->mfakes): ?>
        <dt>Fakes</dt><dd><?php echo $z->yfakes . ($l === "R" ? "/$z->mfakes" : "") ?></dd>
        <?php endif;
        if ($ie === "ie9x"): ?>
        <dt>Preview</dt>
        <dd>
          <?php echo anchor("/chart/quick/$z->id/classic", "Classic", array("title" => "View stepchart using classic Pump it Up arrow colors")); ?>,
          <?php echo anchor("/chart/quick/$z->id/rhythm", "Rhythm", array("title" => "View stepchart with colors based off the rhythm of the song")); ?>
        </dd>
        <?php endif; ?>
      </dl>
    </div>
  </div>
  <div class="edit-right">
    <div class="edit-difficulty" title="<?php echo ucfirst($z->style); ?> Difficulty"><?php echo $z->diff; ?></div>
  </div>
</div>
<?php endforeach; ?>

<?php echo $this->pagination->create_links(); ?>
