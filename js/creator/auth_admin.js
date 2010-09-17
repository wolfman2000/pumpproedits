/*
 * This file handles the functions used for the users
 * that have admin power.
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
	$("#web_yes").click(function(){
		var item = $("#web_sel").val();
		if (item == "hd") { loadHardDrive(); }
		else if (item == "you") { loadOwnEdits(); }
		else if (item == "and") { loadWebEdits(2); }
		else if (item == "off")
		{
			$("li.loadSong").show();
			$("li[class^=load]:not(.loadSong)").hide();
			_disable("#loadDifficulty");
			$("#loadSong").val('');
		};
		else if (item == "all")
		{
			$("li.loadOther").show();
			$("li[class^=load]:not(.loadOther)").hide();
		};
	});
});

