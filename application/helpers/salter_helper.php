<?php
/*
PHP file used for Pump Pro Edits

@package pumpproedits
@author wolfman2000
@license GNU Affero GPL v3 or later
*/

function genSalt($length = 5)
{
	if ($length < 1) { return ''; }
	$slt = '';
	$min = APP_MIN_SALT_CHAR;
	$max = APP_MAX_SALT_CHAR;
	for ($i = 0; $i < $length; $i++)
	{
		$slt .= chr(rand($min, $max));
	}
	return $slt;
}
