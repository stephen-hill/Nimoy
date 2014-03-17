<?php

use Nimoy\Session;

require_once('vendor' . DIRECTORY_SEPARATOR . 'autoload.php');

$session = new Session(array(
	'duration' => 2419200, //28 days
	'name' => 'ExampleNimoySession',
	'provider' => new MemcachedProvider()
));

if (isset($session['time']) == false)
{
	$session['time'] = time();
}

var_dump($session);
var_dump($session->getkey());

setcookie(
	$session->getSessionName(),
	$session->getKey(),
	$session->getDuration() + time()
);