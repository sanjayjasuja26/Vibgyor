<?php
namespace app\modules\media;

use app\components\TModule;

/**
 * media module definition class
 */
class Module extends TModule
{

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\media\controllers';

    public $defaultRoute = 'file';

    /**
     * @inheritdoc
     */
    public static function dbFile()
    {
        return __DIR__ . '/db/install.sql';
    }

    public static function getRules()
    {
        return [
            'media/file/<model>/<id:\d+>' => 'media/file/file'
        ];
    }
}
