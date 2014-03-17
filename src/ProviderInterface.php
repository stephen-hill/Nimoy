<?php

namespace Nimoy
{
	interface ProviderInterface
	{
		public function delete($key);
		public function get($key);
		public function set($key, $value, $duration = 0);
	}
}