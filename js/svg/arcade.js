var iter = 0;
var timer = 400;
var centering = 8;

function animateLifts(lifts)
{
	timer = timer; // dummy code.
	lifts.each(function(){
		var x = parseFloat($(this).attr('x'));
		var y = parseFloat($(this).attr('y'));
		var trans = 'scale(SCALE) translate(TX, TY)';
		
		if (!(iter % 2))
		{
			var tx = x * 2 + centering;
			var ty = y * 2 + centering;
			trans = trans.replace('SCALE', '0.5')
				.replace('TX', tx - x)
				.replace('TY', ty - y);
		}
		else
		{
			trans = trans.replace('SCALE', '1')
				.replace('TX', 0)
				.replace('TY', 0);
		}
		/*
		$(this).animate({
			svgX: x,
			svgY: y,
			svgTransform: trans
		}, timer * .75);
		*/
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