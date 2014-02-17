<?php

namespace Nimoy
{
	use InvalidArgumentException;
	
	class Session extends \Pimple
	{
		private $key;
		
		/**
		 * Creates an instance of this class using either an existing session key
		 * or allowing the constructor to generate a new one.
		 * @param string $key (Optional)
		 */
		public function __construct($key = null)
		{
			if ($key === null)
			{
				$this->key = $this->generateKey();
			}
			else
			{
				if (strlen($key) < 64)
				{
					throw new InvalidArgumentException('Argument $key must contain 64 or more characters.');
				}
				
				$this->key = $key;
			}
		}

		public function getKey()
		{
			return $this->key;
		}

		public function regenerate()
		{
			return $this->key = $this->generateKey();
		}
		
		private function generateKey()
		{
			$entropy = array();
			$entropy[] = hash('sha256', mt_rand());
			$entropy[] = hash('sha256', uniqid('', true));
			$entropy[] = hash('sha256', microtime());
			$entropy[] = hash('sha256', memory_get_usage(true));
			$entropy[] = hash('sha256', getmypid());
			$entropy[] = hash('sha256', memory_get_peak_usage(true));
			$entropy[] = hash('sha256', json_encode(get_defined_constants()));
			$entropy[] = hash('sha256', json_encode(get_included_files()));
			$entropy[] = hash('sha256', json_encode(get_loaded_extensions()));
			$entropy[] = hash('sha256', json_encode(ini_get_all()));
			$entropy[] = hash('sha256', json_encode(posix_times()));
			
			return hash('sha256', implode($entropy));
		}
	}
}