<?php

use Nimoy\Session;
use \Pimple;

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $s = new Session();
        $this->assertInstanceOf(Session::class, $s);
    }

    public function testValidKey()
    {
        $key = $_COOKIE['NimoySession'] = '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4a';
        $s = new Session();
        $this->assertEquals($key, $s->getKey());
    }

    public function testRegenerate()
    {
        $s = new Session();
        $a = $s->getKey();
        $b = $s->regenerate();

        $this->assertNotEquals($a, $b);
    }
}