<?php

use Nimoy\MemcachedProvider;

class MemcachedProviderTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @group MemcachedProvider
	 */
	public function testSetGetDelete()
	{
		$m = new MemcachedProvider();
		$m->set('a62f2', 'f32b6');
		$v = $m->get('a62f2');

		$this->assertSame('f32b6', $v);

		$d = $m->delete('a62f2');
		$v = $m->get('a62f2');

		$this->assertTrue($d);
		$this->assertFalse($v);
	}

	/**
	 * @group MemcachedProvider
	 */
	public function testExpires()
	{
		$m = new MemcachedProvider();
		$this->assertTrue
		(
			$m->set('a62f2', 'f32b6', 31556926)
		);
	}
}