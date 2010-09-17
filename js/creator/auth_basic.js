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

// Use this for dynamic button setups.
$(document).ready(function()
{
	$("#web_yes").click(function(){
		var item = $("#web_sel").val();
		if (item == "hd") { loadHardDrive(); }
		else if (item == "you") { loadOwnEdits(); }
	});
});

function loadOwnEdits()
{
	$(".loadSite").show();
	$("li[class^=load]:not(.loadSite)").hide();
	$("#intro").text("Loading your edits...");
	$("#mem_edit").empty();
	$.getJSON(baseURL + '/loadOwnEdits', function(data)
	{
		authID = data['auth'];
		data = data['query'];
		for (var i = 0; i < data.length; i++)
		{
			var out = data[i].title + " (" + data[i].name + ") " + data[i].style.charAt(0).capitalize() + data[i].diff;
			var html = '<option id="' + data[i].id + '">' + out + '</option>';
			$("#mem_edit").append(html);
		}
		_enable("#mem_nogo");
		$("#intro").text("Choose your edit!");
	});
}

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

function setAuthor()
{
	authID = ($("#authorlist").val() == 0 ? authed : 2);
}
