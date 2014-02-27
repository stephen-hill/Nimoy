<?php

namespace Nimoy
{
    use InvalidArgumentException;
    use ArrayObject;

    class Session extends ArrayObject
    {
        protected $key;
        protected $token;
        protected $flash = [];
        protected $provider;

        /**
         * Creates an instance of this class using either an existing session key
         * or allowing the constructor to generate a new one.
         * @param string $key (Optional)
         */
        public function __construct(array $options = null)
        {
            $this->provider = new MemcachedProvider();

            if (isset($_COOKIE['NimoySession']) === true)
            {
                $this->key = $_COOKIE['NimoySession'];
            }
            else
            {
                $this->key = $this->generateKey();
            }

            $token = $this->provider->get($this->key . 'token');
            $flash = $this->provider->get($this->key . 'flash');
            $array = $this->provider->get($this->key . 'array');

            if (is_string($token) === true)
            {
                $this->token = $token;
            }

            if (is_array($flash) === true)
            {
                $this->flash = $flash;
            }

            if (is_array($array) === true)
            {
                $this->exchangeArray($array);
            }
        }

        public function getKey()
        {
            return $this->key;
        }

        public function regenerate()
        {
            $this->key = $this->generateKey();
            $this->token = null;
            $this->flash = null;

            return $this;
        }

        public function save()
        {
            $this->provider->set($key . 'token', $this->token);
            $this->provider->set($key . 'flash', $this->flash);
            $this->provider->set($key . 'array', (array)$this);
        }

        public function destroy()
        {
            $this->provider->delete($key . 'token');
            $this->provider->delete($key . 'flash');
            $this->provider->delete($key . 'array');

            unset($this->key, $this->token, $this->flash);
        }

        public function getToken()
        {
            if ($this->token === null)
            {
                $this->token = hash('adler32', openssl_random_pseudo_bytes(4096));
            }

            return $this->token;
        }

        public function validToken($value)
        {
            return ($value === $this->token);
        }

        public function setFlash($key, $value)
        {
            $this->flash[$key] = $value;
        }

        public function getFlash($key)
        {
            $value = $this->flash[$key];
            unset($this->flash[$key]);
            return $value;
        }

        public function hasFlash($key)
        {
            return isset($this->flash[$key]);
        }

        private function generateKey()
        {
            return hash('sha512', openssl_random_pseudo_bytes(4096));
        }
    }
}