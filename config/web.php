<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
    
        // Merge these into your main config
        'db' => require __DIR__ . '/db.php', // adjust to your project
        'user' => [
            'identityClass' => 'app\modules\subscription\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['site/login'],
        ],
        'session' => [
            'name' => '_subscriptionSession',
        ],
        
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        // Enable queue if not already
        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
        ],
        'request' => [
        'cookieValidationKey' => '045256be476d54a6bba4fc029c2567d3e870b48d2e00424dbaf23eb4a2182b87',
        ],
    ],
    'modules' => [
        'subscription' => [
            'class' => 'app\modules\subscription\Module',
        ],
        'user' => [
            'class' => 'app\modules\user\Module',
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
