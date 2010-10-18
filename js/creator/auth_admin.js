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
});
