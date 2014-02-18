<?php

use Nimoy\Session;

require_once('vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

$session = new Session((isset($_COOKIE['ExampleSession']) ? $_COOKIE['ExampleSession'] : null));

setcookie(
	'ExampleSession',
	$session->getKey(),
	time() + 60
);

var_dump($session);