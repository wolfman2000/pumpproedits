/*
 * This file handles the functions used for the users
 * that are not authenticated at all.
 *
 * Should any users be authenticated, the functions in
 * this file may end up being overridden.
 */
/*
JS file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

// Use this for dynamic button setups.
$(document).ready(function()
{
	$("#web_yes").click(function(){
		loadHardDrive();
	});
});

function loadButtons()
{
	$("li.edit").hide();
	loadHardDrive();
}

function mustValidate()
{
	isDirty = true;
	_disable("#but_save");
	_enable("#but_val");
	_disable("#but_sub");
}

function validationPassed(data)
{
	saveChart(data);
	$("#intro").text("You can save your work!");
	_enable("#but_save");
	_disable("#but_val");
}

// handle the common setup of the menus.
function _commonMenuSetup()
{
	measures = songData.measures;
	$("#scalelist").val(2.5);
	captured = false;
	columns = getCols();
	$("rect[id^=sel]").attr('width', columns * ARR_HEIGHT).hide();
	fixScale(2.5, 600);
    
	loadSVGMeasures();
    
	$("#tabNav a").filter(':first').click();
	$("#navEditTransform span[id$=Check]").text("???");
	$("nav dt.edit").show();
	$("nav dd.edit").show();
	$("nav *.choose").hide();
	if ($("#stylelist").val() !== "routine") { $("nav .routine").hide(); }
	else { $("nav .routine").show(); }
	var phrase = songData.name + " " + $("#stylelist").val().capitalize();
	$("h2").first().text(phrase);
	$("title").text("Editing " + phrase + " — Pump Pro Edits");
	_enable("#but_new");
	_enable("#editName");
}

function setupMenus()
{
	_commonMenuSetup();
	$(".author").hide();
}

// Load up this common data upon starting a new edit.
function _commonInit()
{
	$("#svg_nav audio").remove();
	captured = false;
	clipboard = null;
	songData = null;
	measures = 3; // temp variable.
	columns = 5; // reasonable default.
	$("article").css('height', '50em');
	fixScale(2, 1000,
		5 * ARR_HEIGHT * SCALE + BUFF_LFT + BUFF_RHT,
		ADJUST_SIZE * BEATS_PER_MEASURE * 3 + BUFF_TOP + BUFF_BOT);
	$("title").text("Edit Creator — Pump Pro Edits");
	$("h2").first().text("Edit Creator");
	
	$("nav dt.edit").hide();
	$("nav dd.edit").hide();
	$("nav li[class^=load]").hide();
	$(".loadOther").hide();
	$("#selTop").hide();
	$("#selBot").hide();
	$("#shadow").addClass('hide');
	$("nav *.choose").show();
	_disable("#stylelist");
	_disable("#but_sub");
	_disable("#but_save");
	_disable("#but_val");
	_disable("#but_new");
	_enable("#cho_file");
	
	// reset the drop downs (and corresponding variables) to default values.
	$("#songlist").val('');
	$("#stylelist").val('');
	$("#scalelist").val(2.5);
	$("#quanlist").val(4);
	$("#typelist").val(1);
	$("#playerlist").val(0);
	$("#modelist").val(0);
	$("#editName").val('');
	$("#editDiff").val('');
	editID = 0;
	selMode = 0;
	
	$("#notes g[id^=svg]").empty();
	
	$("#intro").text("Select your action.");
	
	isDirty = false;
	_enable("#but_load");
	_enable("#songlist");
	
	$("#loadDifficulty").val("");
	$("#loadSong").val("");
	_disable("#song_yes");
	loadSongList("#songlist");
}

// Load up this data on new.
function init()
{
	_commonInit();
	_disable("#cho_site");
}

// Common place to load textarea stuff
function _customTextArea(data)
{
	loadTextEdit(data);
	editID = 0;
	$("#intro").text("All loaded up!");
	_enable("#but_save");
	_disable("#but_val");
	isDirty = false;
	sortArrows();
}

function loadTextArea(data)
{
	_customTextArea(data);
	$(".author").hide();
	_disable("#authorlist");
}
