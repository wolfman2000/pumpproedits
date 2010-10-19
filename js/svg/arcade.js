var iter = 0;
var timer = 400;
var centering = 8;

function animateLifts(lifts)
{
	lifts.each(function(){
		var x = parseFloat($(this).attr('x'));
		var y = parseFloat($(this).attr('y'));
		var trans = 'scale(SCALE)';
		
		if (!(iter % 2))
		{
			trans = trans.replace('SCALE', '0.5');
			$(this).attr('x', x * 2 + centering);
			$(this).attr('y', y * 2 + centering);
			//$(this).attr('x', parseFloat($(this).attr('x')) + 16);
		}
		else
		{
			trans = trans.replace('SCALE', '1');
			$(this).attr('x', (x - centering) / 2);
			$(this).attr('y', (y - centering) / 2);
			//$(this).attr('x', parseFloat($(this).attr('x')) - 16);
		}
		$(this).attr('transform', trans);
	});
	iter++;
}

$(document).ready(function()
{
	$("use[name=lift]").doTimeout(timer, function(){
		animateLifts($(this));
		return true;
	});
});