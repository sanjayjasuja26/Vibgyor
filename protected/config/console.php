<?php

if (php_sapi_name() != "cli") {
    echo 'Please run this file from command line interface !!!';
    exit();
}
$params = require (__DIR__ . '/params.php');

$config = [
    'id' => PROJECT_ID,
    'name' => PROJECT_NAME,
    'basePath' => PROTECTED_PATH,
    'bootstrap' => [
        'log'
    ],
    'vendorPath' => VENDOR_PATH,
    'timeZone' => date_default_timezone_get(),
    'controllerNamespace' => 'app\commands',
    'controllerMap' => [
        'clear' => 'app\components\commands\ClearController',
        'module' => 'app\components\commands\ModuleController',
        'user' => 'app\components\commands\UserController',
    ],
    'components' => [
        'settings' => [
            'class' => 'app\components\Settings'
        ],
        'urlManager' => [
            'class' => 'app\components\SUrlManager',
            'baseUrl' => (YII_ENV == 'dev') ? 'http://localhost/yii2-base-admin-panel-api' : ''
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => [
                        'error',
                        'warning'
                    ]
                ]
            ]
        ],
        'mailer' => [
            'class' => 'app\components\SMailer',
            'useFileTransport' => YII_ENV == 'dev' ? true : false
        ]
    ],
    'params' => $params
];

if (file_exists(DB_CONFIG_FILE_PATH)) {
    $config['components']['db'] = require (DB_CONFIG_FILE_PATH);
}

$config['modules']['installer'] = [
    'class' => 'app\modules\installer\Module',
    'sqlfile' => [
        DB_BACKUP_FILE_PATH . '/install.sql'
    ]
];
$config['modules']['backup'] = [
    'class' => 'app\modules\backup\Module'
];
return $config;
