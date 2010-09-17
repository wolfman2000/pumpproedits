/*
 * This file handles the functions used for the users
 * that are authenticated to make their own edits
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

function loadButtons()
{
	$("li.edit").hide();
	$("li.loadWeb").show();
	$("li[class^=load]:not(.loadWeb)").hide();
	$("#intro").text("Select your option.");
}

function validationPassed(data)
{
	saveChart(data);
	$("#intro").text("You can save your work!");
	_enable("#but_save");
	_disable("#but_val");
	_enable("#but_sub");
}
