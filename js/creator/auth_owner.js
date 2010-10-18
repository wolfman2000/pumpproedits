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
	$("#web_yes").unbind().click(function(){
		var item = $("#web_sel").val();
		if (item == "hd") { loadHardDrive(); }
		else if (item == "you") { loadOwnEdits(); }
		else if (item == "and") { loadOfficialChoices(); }
		else if (item == "off")
		{
			loadSongList("#loadSong");
			$("li.loadSong").show();
			$("li[class^=load]:not(.loadSong)").hide();
			_disable("#loadDifficulty");
		}
		else if (item == "all")
		{
			$.getJSON(baseURL + "/getOtherUsersWithEdits", function(data, status)
			{
				if (isEmpty(data.alert))
				{
					$("#other_sel").empty();
					data = data.peeps;
					for (var i = 0; i < data.length; i++)
					{
						var out = data[i]['name'];
						var html = '<option value="' + data[i]['id'] + '">' + out + '</option>';
						$("#other_sel").append(html);
					}
					$("#other_sel").val(data[0]['id']);
					$("li.loadOther").show();
					$("li[class^=load]:not(.loadOther)").hide();
				}
				else
				{
					// cause an alert
				}
			});
		}
	});
	
	// The admin wishes to select another author's edit.
	$("#other_yes").click(function(){
		loadWebEdits($("#other_sel").val());
	});
	
	// The admin has chosen a song, and needs to choose a difficulty.
	$("#loadSong").change(function(){
		songID = $("#loadSong").val();
		if (songID.length > 0)
		{
			$("#intro").text("Setting up styles...")
			_disable("#loadDifficulty");
			$.getJSON(baseURL + "/songDifficulties/" + songID, function(data, status)
			{
				var diff = $("#loadDifficulty").val();
				if (data.isRoutine > 0)
				{
					$("#loadDifficulty > option:last-child").show();
				}
				else
				{ 
					$("#loadDifficulty > option:last-child").hide();
					if (diff == "rt")
					{
						$("#loadDifficulty").val("");
						_disable("#song_yes");
					}
				}
				_enable("#loadDifficulty");
			});
		}
		else
		{
			_disable("#loadDifficulty");
			_enable("#song_yes");
		}
	});
	
	// The admin has chosen a song and style, and thus can load the chart.
	$("#loadDifficulty").change(function(){
		if ($("#loadDifficulty").val().length) { _enable("#song_yes"); }
		else                                   { _disable("#song_yes"); }
	});
	
	// The admin is ready to load the chart (if it exists)
	$("#song_yes").click(function(){ songMode(); });
});

// Enter this mode of an admin edits an official song.
// Similar to EditMode, but separation is needed.
function songMode()
{
  $("#intro").text("Loading chart...");
  $("li[class^=load]").hide();
  _disable("#authorlist");
  songID = $("#loadSong").val();
  var diff = $("#loadDifficulty").val();
  $("#notes > g").children().remove(); // remove the old chart.
  $(".edit").hide();
  $("#editName").val('');
  $("#editDiff").val('');
  $("#statS").text(0);
  $("#statJ").text(0);
  $("#statH").text(0);
  $("#statM").text(0);
  $("#statR").text(0);
  $("#statT").text(0);
  $("#statF").text(0);
  $("#statL").text(0);
  $("#fCont").val('');
  $("#editName").attr('maxlength', 32);
  $("#editSong").text("Edit Author:");
  $("#but_sub").attr('name', 'songSubmit');
  
  $.getJSON(baseURL + "/loadOfficial/" + songID + "/" + diff, function(data){
    $("#intro").text("Loading chart...");
    $(".author").hide();
    var tmp = "<option value=\"" + data.style + "\">Tmp</option>";
    $("#stylelist").append(tmp);
    $("#stylelist").val(data.style);
    
    songData = data.songData;
    measures = songData['measures'];
    $("#scalelist").val(2.5);
    captured = false;
    columns = getCols();
    $("rect[id^=sel]").attr('width', columns * ARR_HEIGHT).hide();
    fixScale(2.5, 600);
    
    $("#tabNav a").filter(':first').click();
    $("#navEditTransform span[id$=Check]").text("???");
    $("nav dt.edit").show();
    $("nav dd.edit").show();
    
    $("nav *.choose").hide();
    if ($("#stylelist").val() !== "routine") { $("nav .routine").hide(); }
    else { $("nav .routine").show(); }
    var phrase = songData.name + " " + data.title;
    $("h2").first().text(phrase);
    $("title").text("Editing " + phrase + " — Pump Pro Edits");
    _enable("#but_new");
    _enable("#editName");
    _enable("#but_load");
    $("#editName").val(data.author);
    $("#editDiff").val(data.diff);
    
    loadDatabaseChart(data.notes);
    if (data.notes) { updateStats(data); }
    
    isDirty = false;
    clipboard = null;
    $("li.edit").show();
    $("#intro").text("All loaded up!");
  });
}


// Upload the intended official chart.
function uploadOfficial()
{
	var data = {};
	data['b64'] = $("#b64").val();
	data['songID'] = songID;
	data['dShort'] = songData.dShort;
	data['difficulty'] = songData.difficulty;
	data['style'] = $("#stylelist").val();
	$("#intro").text("Uploading chart...");
	$.post(baseURL + "/uploadOfficial", data, function(data, status)
	{
		$("#intro").text("Chart Uploaded");
		_disable("#authorlist");
	}, "json");
}
