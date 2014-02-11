<?php

use Nimoy\Session;
use \Pimple;

class SessionTest extends PHPUnit_Framework_TestCase
{
	public function testConstructor()
	{
		$s = new Session();
		$this->assertInstanceOf(Session::class, $s);
		$this->assertInstanceOf(Pimple::class, $s);
	}
}