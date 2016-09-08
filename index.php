<?php

require __DIR__.'/global.php';

session_start();


Lib\Core\Http::routeControllerAction();


