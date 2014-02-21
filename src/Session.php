<?php

namespace Nimoy
{
    use InvalidArgumentException;
    use ArrayObject;

    class Session extends ArrayObject
    {
        private $key;
        private $name = '';

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
            $this->key = $this->generateKey();
            $this->token = null;

            return $this;
        }

        public function save()
        {

        }

        public function destroy()
        {

        }

        public function getToken()
        {
            if ($this->token === null)
            {
                $this->token = hash('ripemd128', openssl_random_pseudo_bytes(16));
            }

            return $this->token;
        }

        public function validToken($value)
        {
            $return = ($value === $this->token);
            $this->token = null;
            return $return;
        }

        private function generateKey()
        {
            return hash('sha512', openssl_random_pseudo_bytes(4096));
        }
    }
}