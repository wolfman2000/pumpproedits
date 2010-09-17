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
		else if (item == "and") { loadWebEdits(2); }
		else if (item == "off")
		{
			$("li.loadSong").show();
			$("li[class^=load]:not(.loadSong)").hide();
			_disable("#loadDifficulty");
			$("#loadSong").val('');
		}
		else if (item == "all")
		{
			$("li.loadOther").show();
			$("li[class^=load]:not(.loadOther)").hide();
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

