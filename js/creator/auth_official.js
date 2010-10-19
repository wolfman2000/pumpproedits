/*
 * This file handles the functions used for the users
 * that are authenticated to make official edits.
 *
 * Should more power be given to the user, the functions in
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
	$("#web_yes").unbind().click(function(){
		var item = $("#web_sel").val();
		if (item == "hd") { loadHardDrive(); }
		else if (item == "you") { loadOwnEdits(); }
		else if (item == "and") { loadOfficialChoices(); }
	});
	
	$("#official_yes").click(function(){
		var item = $("#official_sel").val();
		if (item == "arc") { loadWebEdits(97); }
		else if (item == "ano") { loadWebEdits(113); }
		else if (item == "max") { loadWebEdits(120); }
		else if (item == "gan") { loadWebEdits(124); }
	});
	
	$("#authorlist").change(function(){
		mustValidate();
	});
});

function setupMenus()
{
	_commonMenuSetup();
	$(".author").hide();
	$("#authorlist").val(0);
	$(".author").show();
	_enable("#authorlist");
}

function loadOfficialChoices()
{
	loadSongList("#loadOfficial");
	$("li.loadOfficial").show();
	$("li[class^=load]:not(.loadOfficial)").hide();
	_disable("#loadDifficulty");	
}

function loadTextArea(data)
{
	_customTextArea(data);
	$(".author").show();
	_enable("#authorlist");
}
