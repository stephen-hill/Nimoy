<?php

/**
 * @package nimoy
 * @version v0.2.0
 * @author Stephen Hill <stephen@gatekiller.co.uk>
 * @copyright Copyright 2014 Stephen Hill
 * @license MIT
 * @link https://github.com/stephen-hill/Nimoy
 */

namespace Nimoy
{
    use InvalidArgumentException;
    use ArrayObject;

    class Session extends ArrayObject
    {
        /**
         * @var string $key The key for this session.
         */
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
            // Declare default hash functions
            $this['key_hash'] = function()
            {
                return hash('sha512', openssl_random_pseudo_bytes(4096));
            };

            $this['token_hash'] = function()
            {
                return hash('adler32', openssl_random_pseudo_bytes(4096));
            };

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

        /**
         * @since v0.2.0
         */
        public function __destruct()
        {
            $this->save();
        }

        /**
         * @since v0.1.0
         */
        public function getKey()
        {
            return $this->key;
        }

        /**
         * @since v0.2.0
         */
        public function getSessionName()
        {
            return $this->sessionName;
        }

        /**
         * @since v0.2.0
         */
        public function getExpires()
        {
            return $this->expires;
        }

        /**
         * @since v0.1.0
         */
        public function regenerate()
        {
            $this->key = $this->generateKey();
            $this->token = null;
            $this->flash = null;

            return $this;
        }

        /**
         * @since v0.2.0
         * @return self
         */
        public function save()
        {
            $this->provider->set($this->key . 'token', $this->token, $this->expires);
            $this->provider->set($this->key . 'flash', $this->flash, $this->expires);
            $this->provider->set($this->key . 'array', (array)$this, $this->expires);

            return $this;
        }

        /**
         * @since v0.2.0
         */
        public function destroy()
        {
            $this->provider->delete($key . 'token');
            $this->provider->delete($key . 'flash');
            $this->provider->delete($key . 'array');

            unset($this->key, $this->token, $this->flash);
        }

        /**
         * @return string The value of the token.
         */
        public function getToken()
        {
            if ($this->token === null)
            {
                $this->token = $this['token_hash']();
            }

            return $this->token;
        }

        /**
         * @param string $value The string value of the token.
         * @return bool Returns true if the supplied token is valid, otherwise it returns false.
         */
        public function validToken($value)
        {
            return ($value === $this->token);
        }

        /**
         * @since v0.2.0
         * @param string $key The key for the flash message.
         * @param mixed $value The data you want saved to the flash message.
         */
        public function setFlash($key, $value)
        {
            $this->flash[$key] = $value;
        }

        /**
         * @since v0.2.0
         * @param string $key The key for the flash message you want to retrieve.
         */
        public function getFlash($key)
        {
            $value = $this->flash[$key];
            unset($this->flash[$key]);
            return $value;
        }

        /**
         * @since v0.2.0
         * @param string $key The key for the flash message you want to check exists.
         */
        public function hasFlash($key)
        {
            return isset($this->flash[$key]);
        }

        /**
         * @since v0.1.0
         * @return string Return's a brand new hash.
         */
        private function generateKey()
        {
            return $this['key_hash']();
        }
    }
}