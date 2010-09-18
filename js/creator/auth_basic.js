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
	
	// Load the account holder's specific edit.
	$("#mem_load").click(function(){
		$("#intro").text("Loading edit...");
		editID = $("#mem_edit > option:selected").attr('id');
		$.getJSON(baseURL + "/loadWebEdit/" + editID, function(data) {
			loadEdit(data);
			$("#intro").text("All loaded up!");
			_disable("#authorlist");
			$("li.author:eq(0)").next().andSelf().hide();
			$("li.author:eq(2)").next().andSelf().show();
			isDirty = false;
			authID = parseInt(data.authID);
		});
	});
});

// set up some of the common load code.
function _displayEditList()
{
	$(".loadSite").show();
	$("li[class^=load]:not(.loadSite)").hide();
	$("#intro").text("Loading the chosen edits...");
	$("#mem_edit").empty();
}

// populate the drop down for the edits.
function _populateEditList(data)
{
	for (var i = 0; i < data.length; i++)
	{
		var out = data[i].title + " (" + data[i].name + ") " + data[i].style.charAt(0).capitalize() + data[i].diff;
		var html = '<option id="' + data[i].id + '">' + out + '</option>';
		$("#mem_edit").append(html);
	}
	_enable("#mem_nogo");
	$("#intro").text("Choose your edit!");
}

// Load up the chosen user's songs.
function loadWebEdits(user)
{
	authID = user;
	_displayEditList();
	$.getJSON(baseURL + '/loadEditList/' + user, function(data)
	{
		_populateEditList(data['query']);
	});
}

function loadOwnEdits()
{
	_displayEditList();
	$.getJSON(baseURL + '/loadOwnEdits', function(data)
	{
		authID = data['auth'];
		_populateEditList(data['query']);
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
	authID = ($("#authorlist").val() == 0 ? "person" : "andamiro");
}

// Upload the intended edit.
function uploadEdit()
{
	var data = {};
	data['b64'] = $("#b64").val();
	data['title'] = $("#editName").val();
	data['diff'] = $("#editDiff").val();
	data['style'] = $("#stylelist").val();
	data['editID'] = editID;
	data['songID'] = songID;
	data['userID'] = authID;
	data['notes'] = $("#noteJSON").val(); // JSON'ed.
	data['public'] = $("#editPublic").val();
	data['radar'] = $("#radar").val(); // underline separator
	data['steps1'] = $("#statS").text().split("/")[0];
	data['steps2'] = $("#statS").text().split("/")[1];
	data['jumps1'] = $("#statJ").text().split("/")[0];
	data['jumps2'] = $("#statJ").text().split("/")[1];
	data['holds1'] = $("#statH").text().split("/")[0];
	data['holds2'] = $("#statH").text().split("/")[1];
	data['mines1'] = $("#statM").text().split("/")[0];
	data['mines2'] = $("#statM").text().split("/")[1];
	data['rolls1'] = $("#statR").text().split("/")[0];
	data['rolls2'] = $("#statR").text().split("/")[1];
	data['trips1'] = $("#statT").text().split("/")[0];
	data['trips2'] = $("#statT").text().split("/")[1];
	data['fakes1'] = $("#statF").text().split("/")[0];
	data['fakes2'] = $("#statF").text().split("/")[1];
	data['lifts1'] = $("#statL").text().split("/")[0];
	data['lifts2'] = $("#statL").text().split("/")[1];
	
	$("#intro").text("Uploading edit...");
	$.post(baseURL + "/upload", data, function(res, status)
	{
		if (res.result === "duplicate")
		{
			$("#intro").text("Duplicate title!");
			alert("You already have an edit titled " + data['title']
				+ "\nfor a " + data['style'] + " edit of " + songData.name
				+ ". Please use a different title.");
		}
		else
		{
			$("#intro").text("Edit Uploaded");
			_disable("#authorlist");
			$("li.author:eq(0)").next().andSelf().hide();
			editID = res.editid;
		}
	}, "json");
}

function setupMenus()
{
	_commonMenuSetup();
	$(".author").hide();
	$("#authorlist").val(0);
	$(".author").hide();
	_disable("#authorlist");
	$("li.author:eq(2)").next().andSelf().show();
}

// Load up this data on new.
function init()
{
	_commonInit();
	_enable("#cho_site");
}
