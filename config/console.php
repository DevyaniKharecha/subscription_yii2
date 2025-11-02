<?php

Yii::setAlias('@tests', dirname(__DIR__) . '/tests');

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'queue'],
    'controllerNamespace' => 'app\console\controllers', 
    'components' => [
        'db' => require __DIR__ . '/db.php',

        'queue' => [
            'class' => \yii\queue\db\Queue::class,
            'db' => 'db',
            'tableName' => '{{%queue}}',
            'channel' => 'default',
            'mutex' => \yii\mutex\MysqlMutex::class,
            'as log' => \yii\queue\LogBehavior::class,
        ],

        'log' => [
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'defaultRoles' => ['user'], // optional: assign default role
        ],
    ],
    

    'controllerMap' => [
        'queue' => [
            'class' => \yii\queue\db\Command::class,
        ],
    ],
];


// $params = require __DIR__ . '/params.php';
// $db = require __DIR__ . '/db.php';

// $config = [
//     'id' => 'basic-console',
//     'basePath' => dirname(__DIR__),
//     'bootstrap' => ['log'],
//     'controllerNamespace' => 'app\commands',
//     'aliases' => [
//         '@bower' => '@vendor/bower-asset',
//         '@npm'   => '@vendor/npm-asset',
//         '@tests' => '@app/tests',
//     ],
    
//     'components' => [
//         'queue' => [
//             'class' => \yii\queue\db\Queue::class,
//             'db' => 'db', // your DB connection
//             'tableName' => '{{%queue}}', // table name
//             'channel' => 'default', // queue channel
//             'mutex' => \yii\mutex\MysqlMutex::class,
//         ],
//         'authManager' => [
//             'class' => 'yii\rbac\DbManager',
//         ],
//         'db' => require __DIR__ . '/db.php',
//     ],
//     'params' => $params,
//     'controllerMap' => [
//         'trial' => [
//             'class' => 'app\console\controllers\TrialController',
//         ],
//         'queue' => [
//             'class' => \yii\queue\cli\Command::class,
//         ],
//     ],
//     /*
//     'controllerMap' => [
//         'fixture' => [ // Fixture generation command line.
//             'class' => 'yii\faker\FixtureController',
//         ],
//     ],
//     */
// ];

// if (YII_ENV_DEV) {
//     // configuration adjustments for 'dev' environment
//     $config['bootstrap'][] = 'gii';
//     $config['modules']['gii'] = [
//         'class' => 'yii\gii\Module',
//     ];
//     // configuration adjustments for 'dev' environment
//     // requires version `2.1.21` of yii2-debug module
//     $config['bootstrap'][] = 'debug';
//     $config['modules']['debug'] = [
//         'class' => 'yii\debug\Module',
//         // uncomment the following to add your IP if you are not connecting from localhost.
//         //'allowedIPs' => ['127.0.0.1', '::1'],
//     ];
// }

// return $config;
