<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/
?>
<nav id="svg_nav" title="Edit Creator Draggable Control Panel">
<p id="intro">Javascript required!</p>
<?php echo form_open_multipart('create/download', array('id' => 'svg_nav_form')); ?>
<dl>
<dt class="buttons">
<input type="hidden" id="abbr" name="abbr" value="BOGUS" />
<input type="hidden" id="b64" name="b64" value="longvalue" />
<input type="hidden" id="noteJSON" name="noteJSON" value="fillThis" />
<input type="hidden" id="radar" name="radar" value="need10" />
<input type="hidden" id="style" name="style" value="none" />
<input type="hidden" id="diff" name="diff" value="π" />
<input type="hidden" id="title" name="title" value="not empty" />
</dt>
<dd class="buttons"><ul id="topButtons">
<li><button id="but_new" type="button" class="fg-button ui-state-default ui-corner-all">New</button></li>
<li><button id="but_help" type="button" class="fg-button ui-state-default ui-corner-all">Help</button></li>
<li><button id="but_load" type="button" class="fg-button ui-state-default ui-corner-all">Load</button></li>
<li><button id="but_val" type="button" class="fg-button ui-state-default ui-corner-all">Validate</button></li>
<li class="loadWeb long reset">What are you editing?</li>
<li class="loadWeb long reset"><select id="web_sel"></select></li>
<li class="loadWeb"><button id="web_yes" type="button" class="fg-button ui-state-default ui-corner-all">Select</button></li>
<li class="loadWeb"><button id="web_no" type="button" class="fg-button ui-state-default ui-corner-all">Nevermind</button></li>
<li class="loadOther long reset">Choose a person for edits...carefully.</li>
<li class="loadOther long"><select id="other_sel"></select></li>
<li class="loadOther"><button id="other_yes" type="button" class="fg-button ui-state-default ui-corner-all">Select</button></li>
<li class="loadOther"><button id="other_no" type="button" class="fg-button ui-state-default ui-corner-all">Nevermind</button></li>
<li class="loadSong long reset"><label for="loadSong">Select your song!</label></li>
<li class="loadSong long"><select id="loadSong" name="loadSong"></select></li>
<li class="loadSong long"><label for="loadDifficulty">Select your difficulty!</label></li>
<li class="loadSong long"><select id="loadDifficulty" name="loadDifficulty">
<option value="">Choose!</option>
<option value="ez">Easy</option>
<option value="nr">Normal</option>
<option value="hr">Hard</option>
<option value="cz" selected="selected">Crazy</option>
<option value="hd">Halfdouble</option>
<option value="fs">Freestyle</option>
<option value="nm">Nightmare</option>
<option value="rt">Routine</option>
</select></li>
<li class="loadSong"><button id="song_yes" type="button" class="fg-button ui-state-default ui-corner-all">Select</button></li>
<li class="loadSong"><button id="song_no" type="button" class="fg-button ui-state-default ui-corner-all">Nevermind</button></li>
<li class="loadSite long">Select your edit below.</li>
<li class="loadSite long"><select id="mem_edit"></select></li>
<li class="loadSite reset"><button id="mem_load" type="button" class="fg-button ui-state-default ui-corner-all">Load Edit</button></li>
<li class="loadSite"><button id="mem_nogo" type="button" class="fg-button ui-state-default ui-corner-all">Nevermind</button></li>
<li class="loadFile long reset">Paste the edit contents below.</li>
<li class="loadFile long reset"><textarea id="fCont" name="fCont"></textarea></li>
<li class="loadFile reset"><button id="but_file" type="button" class="fg-button ui-state-default ui-corner-all">Load File</button></li>
<li class="loadFile"><button id="rem_file" type="button" class="fg-button ui-state-default ui-corner-all">Nevermind</button></li>
<li class="edit"><button id="but_save" type="submit" class="fg-button ui-state-default ui-corner-all">Save</button></li>
<li class="edit"><button id="but_sub" type="button" class="fg-button ui-state-default ui-corner-all">Submit</button></li>
</ul></dd>
<dt class="choose"></dt>
<dd class="choose"><ul id="newEditChoice">
<li><label for="songlist">Select your song!</label></li>
<li><select id="songlist" name="songlist"></select></li>
<li><label for="stylelist">Select your style!</label></li>
<li><select id="stylelist">
<option value="" selected="selected">Choose</option>
</select></li>
</ul>
</dd> <?php /* Everything below is for Edit Mode. */ ?>
<dt class="edit"></dt>
<dd class="edit">

<nav id="tabs">
<ul id="tabNav">
<li><a href="#navEditControls">Controls</a></li>
<li><a href="#navEditInfo">Edit Info</a></li>
<li><a href="#navEditTransform">Transforms</a></li>
</ul>
</nav>

<ul id="allEditInfo">
<li>Present Location:</li>
<li class="reset">Measure <span id="mCheck">???</span></li>
<li>Beat <span id="yCheck">???</span> / 192</li>
</ul>

<ul id="navEditControls">

<li><label for="quanlist">Note Sync:</label></li>
<li><select id="quanlist">
<option value="4" selected="selected">4th</option>
<option value="8">8th</option>
<option value="12">12th</option>
<option value="16">16th</option>
<option value="24">24th</option>
<option value="32">32nd</option>
<option value="48">48th</option>
<option value="64">64th</option>
<option value="192">192nd</option>
</select></li>
<li><label for="typelist">Note Type:</label></li>
<li><select id="typelist">
<option value="1" selected="selected">Tap</option>
<option value="2">Hold Head</option>
<option value="3">Hold/Roll End</option>
<option value="4">Roll Head</option>
<option value="M">Mine</option>
<option value="L">Lift</option>
<option value="F">Fake</option>
</select></li>
<li><label for="scalelist">Chart Zoom:</label></li>
<li><select id="scalelist">
<option value="1">Tiny</option>
<option value="2">Small</option>
<option value="2.5" selected="selected">Normal</option>
<option value="3">Big</option>
<option value="4">Giant</option>
</select></li>
<li><label for="modelist">Cursor Mode:</label></li>
<li><select id="modelist">
<option value="0" selected="selected">Insert</option>
<option value="1">Select</option>
</select></li>
<li class="routine"><label for="playerlist">Routine Player:</label></li>
<li class="routine"><select id="playerlist">
<option value="0" selected="selected">Player 1</option>
<option value="1">Player 2</option>
</select></li>
<li class="sections long reset"><label for="sectionList">Which song section do you want?</label></li>
<li class="sections long reset"><select id="sectionList"></select></li>
<?php /* The li below will become two when/if music is allowed.*/ ?>
<li class="sections long reset"><button id="sectionJump" type="button" class="fg-button ui-state-default ui-corner-all">Jump to Section</button></li>
</ul>

<ul id="navEditInfo">
<li class="author"><label for="authorlist">Edit Author:</label></li>
<li class="author"><select id="authorlist">
<option value="0" selected="selected">Yourself</option>
<option value="1">Andamiro</option>
</select></li>
<li><label id="editSong" for="editName">Edit Name:</label></li>
<li><input type="text" id="editName" maxlength="12" /></li>
<li><label for="editDiff">Diff. Rating:</label></li>
<li><input type="text" id="editDiff" maxlength="2" /></li>
<li class="author"><label for="editPublic">Public?</label></li>
<li class="author"><select id="editPublic">
<option value="1" selected="selected">Yes</option>
<option value="0">No</option>
</select></li>
<li>Step Stats:</li>
<li class="reset">Steps: <span id="statS">0</span></li>
<li>Jumps: <span id="statJ">0</span></li>
<li>Holds: <span id="statH">0</span></li>
<li>Mines: <span id="statM">0</span></li>
<li>Trips: <span id="statT">0</span></li>
<li>Rolls: <span id="statR">0</span></li>
<li>Lifts: <span id="statL">0</span></li>
<li>Fakes: <span id="statF">0</span></li>

</ul>

<ul id="navEditTransform">
<li class="long">First Selected Row:</li>
<li class="reset">Measure <span id="f_mCheck">???</span></li>
<li>Beat <span id="f_yCheck">???</span> / 192</li>
<li class="long">Second Selected Row:</li>
<li class="reset">Measure <span id="s_mCheck">???</span></li>
<li>Beat <span id="s_yCheck">???</span> / 192</li>
<li><button id="transformCut" type="button" class="fg-button ui-state-default ui-corner-all">Cut</button></li>
<li><button id="transformCopy" type="button" class="fg-button ui-state-default ui-corner-all">Copy</button></li>
<?php /*<li class="three"><button id="transformPast" type="button" class="fg-button ui-state-default ui-corner-all">Paste</button></li>*/ ?>
<li><button id="transformRotateLeft" type="button" class="fg-button ui-state-default ui-corner-all">Rotate Left</button></li>
<li><button id="transformRotateRight" type="button" class="fg-button ui-state-default ui-corner-all">Rotate Right</button></li>
<li><button id="transformMoveUp" type="button" class="fg-button ui-state-default ui-corner-all">Move Up</button></li>
<li><button id="transformMoveDown" type="button" class="fg-button ui-state-default ui-corner-all">Move Down</button></li>
<li><button id="transformMirrorSimple" type="button" class="fg-button ui-state-default ui-corner-all">Mirror</button></li>
<li><button id="transformMirrorDiag" type="button" class="fg-button ui-state-default ui-corner-all">Mirror Diagonally</button></li>
</ul>

</dd>
</dl>
</form>
</nav>
