$(document).ready(function()
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