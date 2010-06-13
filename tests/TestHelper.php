<?php

error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('America/New_York');

define('LIBRARY_PATH', dirname(dirname(__FILE__)) . '/library');
define('TEST_DATA_PATH', dirname(__FILE__) . '/data');

set_include_path(implode(PATH_SEPARATOR, array(
	LIBRARY_PATH,
	get_include_path()
)));

require_once 'OExchange.php';
require_once 'PHPUnit/Framework.php';
