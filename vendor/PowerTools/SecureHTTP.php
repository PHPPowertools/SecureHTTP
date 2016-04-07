<?php
namespace PowerTools;

class SecureHTTP {

    protected $_keys;
    public $hashLength = 13;
    public $nonceLength = 31;
    public $lifetime = 200;
    public $salt = '183D$ee87n@ss5';

    public static function factory($options = []) {
        return new static($options);
    }
    
    public function __construct($options = []) {
        $this->_keys = $this->setKeys($options);
    }
    
    public function setKeys($options = []) {
        return SecureHTTP_key::factory($options);
    }

    public function encrypt($content) {
        $keys = $this->_keys;
        $hash = $this->_generateRandomString($this->hashLength);
        $c = $this->_generateRandomString($this->nonceLength);
        $q = $this->_encrypt($content, $keys->consumerSecret, $hash);
        $credentials = [
            'content' => $c,
            'oauth_consumer_key' => $keys->consumer,
            'oauth_signature_method' => $keys->method,
            'oauth_timestamp' => time(),
            'oauth_token' => $keys->token,
            'oauth_version' => '1.0',
            'q' => $q
        ];
        $credentials['oauth_signature'] = rawurlencode(
            base64_encode(
                hash_hmac(
                    'sha1',
                    $keys->method . '&' . rawurlencode($keys->baseURL) . '&' . rawurlencode(http_build_query($credentials)),
                    rawurlencode($keys->consumerSecret) . '&' . rawurlencode($keys->tokenSecret),
                    TRUE
                )
            )
        );
        $credentials['content'] = $this->_encrypt(
            $c . $q,
            $keys->tokenSecret, $hash
        ) . $hash;
        unset($credentials['oauth_signature_method']);
        unset($credentials['oauth_version']);
        unset($credentials['q']);
        return $credentials;
    }

    function decrypt($credentials) {
        if ($credentials['oauth_timestamp'] + $this->lifetime > time()) {
            $keys = $this->_keys->set([
                'consumer' => $credentials['oauth_consumer_key'],
                'token' => $credentials['oauth_token']
            ]);
            $hash = substr($credentials['content'], -1 * $this->hashLength);
            $string = $this->_decrypt(
                substr($credentials['content'], 0, -1 * $this->hashLength),
                $keys->tokenSecret,
                $hash
            );
            $c = substr($string, 0, $this->nonceLength);
            $q = substr($string, $this->nonceLength);
            if ($credentials["oauth_signature"] === rawurlencode(
                    base64_encode(
                        hash_hmac(
                            'sha1',
                            $keys->method . '&' . rawurlencode($keys->baseURL) . '&' . rawurlencode(http_build_query([
                                'content' => $c,
                                'oauth_consumer_key' => $credentials['oauth_consumer_key'],
                                'oauth_signature_method' => $keys->method,
                                'oauth_timestamp' => $credentials['oauth_timestamp'],
                                'oauth_token' => $credentials['oauth_token'],
                                'oauth_version' => '1.0',
                                'q' => $q
                            ])),
                            rawurlencode($keys->consumerSecret) . '&' . rawurlencode($keys->tokenSecret),
                            TRUE
                        )
                    )
                )
            ) {
                return $this->_decrypt(
                    $q,
                    $keys->consumerSecret,
                    $hash
                );
            } else {
                exit(json_encode(["error" => "You are not authorized"]));
            }
        } else {
            exit(json_encode(["error" => "Your message is too old"]));
        }
        return false;
    }
    
    protected function _encrypt($decrypted, $password, $hash) {
        $decrypted = gzcompress(json_encode($decrypted));
        $salt = $this->salt . $hash;
        $key = hash('SHA256', $salt . $password, true);
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC), MCRYPT_RAND);
        if (strlen($iv_base64 = rtrim(base64_encode($iv), '=')) != 22)
            return false;
        $encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $decrypted . md5($decrypted), MCRYPT_MODE_CBC, $iv));
        return urlencode($iv_base64 . $encrypted);
    }

    protected function _decrypt($encrypted, $password, $hash) {
        $salt = $this->salt . $hash;
        $encrypted = urldecode($encrypted);
        $key = hash('SHA256', $salt . $password, true);
        $iv = base64_decode(substr($encrypted, 0, 22) . '==');
        $encrypted = substr($encrypted, 22);
        $decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, base64_decode($encrypted), MCRYPT_MODE_CBC, $iv), "\0\4");
        $hash = substr($decrypted, -32);
        $decrypted = substr($decrypted, 0, -32);
        if (md5($decrypted) != $hash)
            return false;
        return json_decode(gzuncompress($decrypted), TRUE);
    }

    protected function _generateRandomString($length = NULL) {
        $length = NULL ? $this->nonceLength : $length;
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randomString;
    }
}
