<?php

namespace Nimoy
{
	use Memcached;

	class MemcachedProvider implements ProviderInterface
	{
		protected $memcached;
		private $defaultDuration = 60;

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

		public function set($key, $value, $duration = 0)
		{
			if ($duration === 0)
			{
				$duration = 60;
			}

			$duration += time();

			return $this->memcached->set($key, $value, $duration);
		}
	}
}