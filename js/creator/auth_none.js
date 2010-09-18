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

function validationPassed(data)
{
	saveChart(data);
	$("#intro").text("You can save your work!");
	_enable("#but_save");
	_disable("#but_val");
}

function setAuthor()
{
	authID = 0;
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
