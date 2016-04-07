# SecureHTTP Component

***[PHPPowertools](https://github.com/PHPPowertools)*** is a web application framework for PHP >= 5.4.

***[PHPPowertools/SecureHTTP](https://github.com/PHPPowertools/SecureHTTP)*** is a simple library for encrypting + compressing HTTP requests.

##### Example use :

```php
$keyFolder = 'var/secure/keys';


// Encrypt data
$encrypted = \PowerTools\SecureHTTP::factory([
    'consumer' => '153eePP2185e4444$2215153335d1R',
    'token' => '1821dZZ5-4-35-16fj6225ak-DWWZvnk-d1qNl5R',
    'keyFolder' => $keyFolder
])->encrypt($data);


// Decrypt data
$decrypted = \PowerTools\SecureHTTP::factory([
    'keyFolder' => $keyFolder
])->decrypt($encrypted);
```

-----

##### Author

| [![twitter/johnslegers](https://en.gravatar.com/avatar/bf4cc94221382810233575862875e687?s=70)](http://twitter.com/johnslegers "Follow @johnslegers on Twitter") |
|---|
| [John slegers](http://www.johnslegers.com/) |