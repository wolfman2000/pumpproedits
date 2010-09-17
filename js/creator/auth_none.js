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
