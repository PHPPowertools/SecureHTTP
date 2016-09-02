<?php
define("LOCAL_PATH_BOOTSTRAP", __DIR__);
define("LOCAL_PATH_KEYS", dirname(LOCAL_PATH_BOOTSTRAP) . DIRECTORY_SEPARATOR . 'keys');
require LOCAL_PATH_BOOTSTRAP . DIRECTORY_SEPARATOR . 'bootstrap.php';

$data = [
    'string' => 'This is a string',
    'integer' => 10,
    'float' => 15451533.188355,
    'url' => 'http://www.google.com',
    'simple array' => [
        'John', 'An', 'Stan', 'Susan', 'Thomas', 'Mary', 'Beth'
    ],
    'associative array' => [
        'id' => 5,
        'firstName' => 'Francis',
        'lastName' => 'Stevenson',
        'age' => 27,
        'co-workers' => [
            7, 9, 12, 15, 17, 18, 22, 24, 27, 33, 48, 56, 67, 83, 110
        ]
    ],
    'multidimensional array' => [
        'level1A' => [
            'level2A' => [
                'level3' => [
                    'kewl',
                    'this stuff works'
                ]
            ],
            'level2B' => [
                'level3' => [
                    'also kewl',
                    'this stuff REALLY works'
                ]
            ]
        ],
        'level1B' => [
            'I rest my case'
        ]
    ],
    'large string' => htmlentities(file_get_contents(LOCAL_PATH_BOOTSTRAP . DIRECTORY_SEPARATOR . 'lyrics'))
];

$encrypted = \PowerTools\SecureHTTP::factory([
    'consumer' => '153eePP2185e4444$2215153335d1R',
    'token' => '1821dZZ5-4-35-16fj6225ak-DWWZvnk-d1qNl5R',
    'keyFolder' => LOCAL_PATH_KEYS
])->encrypt($data);

$decrypted = \PowerTools\SecureHTTP::factory([
    'keyFolder' => LOCAL_PATH_KEYS
])->decrypt($encrypted);
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" media="all"  href="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_CSS; ?>/cascade-full.min.css" />
        <link rel="stylesheet" type="text/css" media="all"  href="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_CSS; ?>/site.css" />
        <title>SecureHTTP demo</title>
        <meta name="description" content="Boilerplate for PHP PowerTools projects">
        <link rel="shortcut icon" href="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_IMG; ?>/favicon.ico" type="image/x-icon" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body>
        <div class="site-header">
            <div class="site-center">
                <div class="cell">
                    <h1>SecureHTTP demo</h1>
                </div>
            </div>
        </div>
        <div class="site-body">
            <div class="site-center">
                <div class="cell">
                    <p>Data before encryption:</p>
                    <pre><?php echo toJson($data); ?></pre>
                    <p>Data after encryption:</p>
                    <pre><?php echo toJson($encrypted); ?></pre>
                    <p>Data after decryption:</p>
                    <pre><?php echo toJson($decrypted); ?></pre>
                </div>
            </div>
        </div>
        <div class="site-footer">
            <div class="site-center" id="site-footer-content">
                <div class="col">
                    <div class="cell pipes">
                        <ul class="nav">
                            <li>Powered by <a target="_blank" href="http://www.cascade-framework.com">Cascade Framework</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <script src="<?php echo REQUEST_PROTOCOL . HTTP_PATH_ASSET_JS; ?>/app.js"></script>
    </body>
</html>
