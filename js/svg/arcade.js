var iter = 0;

function animateLifts()
{
	if (iter++ % 2)
	{
		
	}
	else
	{
		
	}
}

$(document).ready(function()
{
	$("use[name=lift]").doTimeout(400, function(){
		animateLifts();
		return true;
	});
});