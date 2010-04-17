<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ', 							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE', 					'ab');
define('FOPEN_READ_WRITE_CREATE', 				'a+b');
define('FOPEN_WRITE_CREATE_STRICT', 			'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');


// Constants relating to my own work.
define('APP_BASE_EDITS_PER_PAGE', 30);
define('APP_MAX_EDIT_FILE_SIZE', 61440);
define('APP_MIN_DIFFICULTY_RATING', 1);
define('APP_MAX_DIFFICULTY_RATING', 99);
define('APP_MAX_EDIT_NAME_LENGTH', 12);
define('APP_CHART_SIN_COLS', 4);
define('APP_CHART_DBL_COLS', 8);
define('APP_CHART_HEADER_HEIGHT', 96);
define('APP_CHART_FOOTER_HEIGHT', 32);
define('APP_CHART_ARROW_HEIGHT', 16);
define('APP_CHART_ARROW_WIDTH', 16);
define('APP_CHART_COLUMN_LEFT_BUFFER', 32);
define('APP_CHART_COLUMN_RIGHT_BUFFER', 16);
define('APP_CHART_MEASURE_COL', 6);
define('APP_CHART_BEAT_HEIGHT', 16);
define('APP_CHART_SPEED_MOD', 2);
define('APP_CHART_BEAT_P_MEASURE', 4);
define('APP_CHART_DEF_FILE', '/svg/arrowdef.svg');

/* End of file constants.php */
/* Location: ./system/application/config/constants.php */
