<?php

namespace PowerTools;

class SecureHTTP_key {
    public $consumer;
    public $consumerSecret;
    public $token;
    public $tokenSecret;
    public $baseURL;
    public $method;
    public $keyFolder;

    protected function _getSecret($key, $token = false) {
        $extension = $token ? '.token.key' : '.consumer.key';
        $data = explode('=', @file_get_contents($this->keyFolder . DIRECTORY_SEPARATOR . $key . $extension));
        return $data[1];
    }

    public static function factory($options) {
        return new static($options);
    }
    
    public function __construct($options) {
        $options['method'] = isset($options['method']) ? $options['method'] : 'CUSTOM-HMAC-SHA1';
        $options['baseURL'] = isset($options['baseURL']) ? $options['baseURL'] : (
            isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'http://'
        );
        $this->set($options);
    }
    
    public function set($options) {
        if(isset($options['baseURL'])) {
            $this->baseURL = $options['baseURL'];
        }
        if(isset($options['method'])) {
            $this->method = $options['method'];
        }
        if(isset($options['keyFolder'])) {
            $this->keyFolder = $options['keyFolder'];
        }
        if(isset($options['consumer'])) {
            $this->consumer = $options['consumer'];
            $this->consumerSecret = $this->_getSecret($this->consumer);
        }
        if(isset($options['token'])) {
            $this->token = $options['token'];
            $this->tokenSecret = $this->_getSecret($this->token, true);
        }
        return $this;
    }
}
