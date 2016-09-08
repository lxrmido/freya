<?php

require __DIR__.'/global.php';

session_start();

\Lib\User\User::add_user([
	'username' => 'freya',
	'password' => 'freya',
	'email'    => 'freya@local.host'
]);


