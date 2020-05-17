<?php

/**
 * @author     : Sanjay Jasuja <sanjayjasuja26@gmail.com >
 */
$params = require (__DIR__ . '/params.php');
$config = [
    'id' => PROJECT_ID,
    'name' => PROJECT_NAME,
    'basePath' => PROTECTED_PATH,
    'runtimePath' => RUNTIME_PATH,
    'vendorPath' => VENDOR_PATH,
    'defaultRoute' => 'site/index',
    'language' => 'en',
    'bootstrap' => [
        'log',
        'session',
        'app\components\TBootstrap',
        'languagepicker'
    ],
    'timeZone' => date_default_timezone_get(),
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset'
    ],
    'components' => [
        'session' => [
            'class' => 'app\components\TSession'
        ],
        'request' => [
            'class' => 'app\components\TRequest'
        ],
        'settings' => [
            'class' => 'app\components\Settings'
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache'
        ],
        'user' => [
            'class' => 'app\components\WebUser'
        ],
        'mailer' => [
            'class' => 'app\components\TMailer',
            'useFileTransport' => YII_ENV == 'dev' ? true : false
        ],
        'log' => [
            'traceLevel' => defined('YII_DEBUG') ? 3 : 0,
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
        'formatter' => [
            'class' => 'app\components\formatter\TFormatter',
            'thousandSeparator' => ',',
            'decimalSeparator' => '.',
            'defaultTimeZone' => date_default_timezone_get(),
            'datetimeFormat' => 'php:Y-m-d h:i:s A',
            'dateFormat' => 'php:Y-m-d'
        ],
        'urlManager' => [
            'class' => 'app\components\TUrlManager',
            'rules' => [
                'file/file/files/<file>' => '/file/file/files',
                '<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>/<id:\d+>/<title>' => '<controller>/<action>',
                '<module:[A-Za-z-]+>/<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>/<id:\d+>/<title>' => '<module>/<controller>/<action>',
                '<module:[A-Za-z-]+>/<controller:[A-Za-z-]+>/<id:\d+>/<title>' => '<module><controller>/view',
                '<module:[A-Za-z-]+>/<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>/<id:\d+>' => '<module>/<controller>/<action>',
                '<controller:[A-Za-z-]+>/<id:\d+>/<title>' => '<controller>/view',
                '<controller:[A-Za-z-]+>/<id:\d+>' => '<controller>/view',
                '<controller:[A-Za-z-]+>/<action:[A-Za-z-]+>/<id:\d+>' => '<controller>/<action>',
                '<action:about|contact|privacy|settings|copyright|terms>' => 'site/<action>',
            ]
        ],
        'languagepicker' => [
            'class' => 'lajax\languagepicker\Component',
            'languages' => [
                'ar' => 'Arabic',
                'en' => 'English'
            ]
        ],
        'view' => [
            'theme' => [
                'class' => 'app\components\AppTheme',
                'name' => 'base', // 'admin_pro' 'new','base'
                'style' => 'red' // ['info',danger,warning,success]
            ]
        ]
    ],
    'params' => $params,
    'modules' => [
        'sitemap' => [
            'class' => 'app\modules\sitemap\Module',
            'models' => [
                'app\modules\blog\models\Post'
            ],
            'urls' => [
                [
                    'loc' => '/site/about'
                ],
                [
                    'loc' => '/site/privacy'
                ],
                [
                    'loc' => '/site/terms'
                ],
                [
                    'loc' => '/contact-us'
                ]
            ],
            'enableGzip' => true
        ]
    ],
    'on beforeRequest' => function () {
        //   \Yii::$app->getModule('maintenance')->init();
    }
];

if (file_exists(DB_CONFIG_FILE_PATH)) {
    $config['components']['db'] = require (DB_CONFIG_FILE_PATH);
} else {
    $config['modules']['installer'] = [
        'class' => 'app\modules\installer\Module',
        'sqlfile' => [
            DB_BACKUP_FILE_PATH . '/install.sql'
        ]
    ];
}
if (YII_ENV == 'dev') {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => [
            '127.0.0.1',
            '::1',
            '192.168.10.*'
        ]
    ];

    $config['modules']['tugii'] = [
        'class' => 'app\modules\tugii\Module'
    ];

    $config['components']['errorHandler'] = [
        'errorAction' => 'site/error'
    ];
    $config['components']['assetManager']['forceCopy'] = true;
} else {
    $config['components']['errorHandler'] = [
        'errorAction' => 'logger/log/custom-error'
    ];
}

/* You can use below modules for faster developement */
$config['modules']['logger'] = [
    'class' => 'app\modules\logger\Module'
];
$config['modules']['file'] = [
//    'class' => 'app\modules\file\Module'
    'class' => 'thyseus\files\FileWebModule',
//    
];
$config['modules']['notification'] = [
    'class' => 'app\modules\notification\Module'
];
$config['modules']['api'] = [
    'class' => 'app\modules\api2\Module'
];
$config['modules']['backup'] = [
    'class' => 'app\modules\backup\Module'
];
//$config['modules']['seo'] = [
//    'class' => 'app\modules\seo\Module'
//];
$config['modules']['page'] = [
    'class' => 'app\modules\page\Module'
];
$config['modules']['comment'] = [
    'class' => 'app\modules\comment\Module'
];

// $config['modules']['rating'] = [
// 'class' => 'app\modules\rating\Module'
// ];
$config['modules']['translator'] = [
    'class' => 'app\modules\translator\Module'
];
// $config['modules']['statistics'] = [
// 'class' => 'app\modules\statistics\Module'
// ];
// $config['modules']['system'] = [
// 'class' => 'app\modules\system\Module'
// ];
// $config['modules']['blog'] = [
// 'class' => 'app\modules\blog\Module'
// ];
return $config;
