/*
JS file for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/$(document).ready(function()
{
	$("#edits").val("無");
	$("#submit").attr('disabled', 'disabled');
	
	$("#edits").change(function()
	{
		$("#submit").attr('disabled', 'disabled');
		if (!isNaN($("#edits").val()))
		{
			$("#submit").removeAttr('disabled');
		}
	});
});
