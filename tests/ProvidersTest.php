<?php

use Nimoy\MemcachedProvider;

/**
 * @todo Implement a test for invalid keys
 */
class MemcachedProviderTest extends PHPUnit_Framework_TestCase
{
	private $providers = array();

	public function setUp()
	{
		$this->providers['Memcached'] = new MemcachedProvider();
	}

	/**
	 * @group Providers
	 */
	public function testSetGetTypes()
	{
		foreach ($this->providers as $n => $p)
		{
			// Test String
			$p->set('StringKey', 'Hello World');
			$this->assertSame('Hello World', $p->get('StringKey'));

			// Test Integers
			$p->set('IntegerKey', 1234);
			$this->assertSame(1234, $p->get('IntegerKey'));

			// Test stdClass
			$p->set('stdClassKey', new stdClass());
			$this->assertEquals(new stdClass(), $p->get('stdClassKey'));

			// Test Array
			$p->set('arrayKey', ['key' => 'value']);
			$this->assertSame(['key' => 'value'], $p->get('arrayKey'));

			// Test Float
			$p->set('floatKey', 1.10);
			$this->assertSame(1.10, $p->get('floatKey'));

			// Test Binary
			$p->set('binaryKey', 0b001001101);
			$this->assertSame(0b001001101, $p->get('binaryKey'));

			// Test Delete
			$p->set('DeleteMe', 'Please Delete This.');
			$this->assertTrue($p->delete('DeleteMe'));

			// Test long expiry time
			$this->assertTrue($p->set('key', 'value', 31556926));

			// Test short expiry time
			$this->assertTrue($p->set('key', 'value', 1));
		}
	}
}