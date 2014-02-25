<?php

namespace Nimoy
{
	use Memcached;

	class MemcachedProvider implements ProviderInterface
	{
		protected $memcached;

		public function __construct($host = '127.0.0.1', $port = 11211)
		{
			$this->memcached = new Memcached();
			$this->memcached->addServer($host, $port);
		}

		public function delete($key)
		{
			return $this->memcached->delete($key);
		}

		public function get($key)
		{
			return $this->memcached->get($key);
		}

		public function set($key, $value, $expires = 0)
		{
			if ($expires > (60*60*24*30))
			{
				$expires += time();
			}

			return $this->memcached->set($key, $value, $expires);
		}
	}
}