<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id'         => 'basic',
    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],
    'aliases'    => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request'    => [
            'cookieValidationKey' => 'GIurEtpwAS0qSW6GyHBL8Sa6Ruj0gFL7',
            'parsers'             => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'cache'      => [
            'class' => 'yii\caching\FileCache',
        ],
        'user'       => [
            'identityClass' => 'app\models\User',
            'enableSession' => false,
            'loginUrl'      => null
        ],
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'  => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'         => $db,
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'rules'               => [
                'api/v1/register' => 'site/register',
                'api/v1/login'    => 'site/login',
                ['class' => 'yii\rest\UrlRule', 'controller' => 'note', 'prefix' => 'api/v1/',],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'todo', 'prefix' => 'api/v1/',],
            ],
        ]
    ],
    'params'     => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class'      => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class'      => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
