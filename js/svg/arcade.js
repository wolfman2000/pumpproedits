var iter = 0;

function animateLifts(lifts)
{
	lifts.each(function(){
		if (iter % 2)
		{
			$(this).attr('x', parseFloat($(this).attr('x')) + 16);
		}
		else
		{
			$(this).attr('x', parseFloat($(this).attr('x')) - 16);
		}
	});
	iter++;
}

$(document).ready(function()
{
	$("use[name=lift]").doTimeout(400, function(){
		animateLifts($(this));
		return true;
	});
});