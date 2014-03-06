<?php

/**
 * @package nimoy
 * @version 0.2
 * @author Stephen Hill <stephen@gatekiller.co.uk>
 * @copyright Copyright 2014 Stephen Hill
 * @license MIT
 */

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
        protected $expires;
        protected $sessionName;

        /**
         * Create an instance of this class
         * @param array $options (Optional)
         * @since v0.1.0
         */
        public function __construct(array $options = array())
        {
            $defaults = array(
                'expires' => time() + 60,
                'provider' => new MemcachedProvider(),
                'name' => 'DefaultSession'
            );

            $options = array_merge($defaults, $options);

            $this->provider = $options['provider'];
            $this->sessionName = $options['name'];
            $this->expires = $options['expires'];

            if (isset($_COOKIE[$this->sessionName]) === true)
            {
                $this->key = $_COOKIE[$this->sessionName];
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

        public function __destruct()
        {
            $this->save();
        }

        public function getKey()
        {
            return $this->key;
        }

        public function getSessionName()
        {
            return $this->sessionName;
        }

        public function getExpires()
        {
            return $this->expires;
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
            $this->provider->set($this->key . 'token', $this->token, $this->expires);
            $this->provider->set($this->key . 'flash', $this->flash, $this->expires);
            $this->provider->set($this->key . 'array', (array)$this, $this->expires);
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