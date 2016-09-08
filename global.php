<?php

require __DIR__.'/config.php';
require __DIR__.'/vendor/autoload.php';

$_RG = [
	'rewrite'    => RUNTIME_REWRITE,
	'url_root'   => WEBSITE_URL_ROOT,
	'url_static' => WEBSITE_URL_ROOT . '/' . RUNTIME_DIR_STATIC
];

$FREYA_CONSOLE      = false;
$FREYA_JSON_IO      = false;
$FREYA_JSONP        = false;
$FREYA_DIE_ON_ERROR = true;
$FREYA_INNER_CALL   = false;
$FREYA_ARGS         = array();
$FREYA_METHOD       = array(
    'is_method' => false,
    'debug'    => array()
);

date_default_timezone_set(TIME_ZONE);


