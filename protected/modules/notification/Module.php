<?php
namespace app\modules\notification;

use app\components\SController;
use app\components\SModule;
use Yii;

/**
 * notification module definition class
 */
class Module extends SModule
{

    /**
     *
     * {@inheritdoc}
     */
    public $controllerNamespace = 'app\modules\notification\controllers';

    public $defaultRoute = 'notification';

    public $isNotify = true;

    public static function dbFile()
    {
        return __DIR__ . '/db/install.sql';
    }

   /*  public static function subNav()
    {
        if (method_exists("\app\components\WebUser", 'getIsAdminMode'))
            if (\Yii::$app->user->isAdminMode) {
                return self::adminSubNav();
            }
        return SController::addMenu(Yii::t('app', 'Notifications'), '//notification', 'list-alt', (Module::isManager()));
    }

    public static function adminSubNav()
    {
        return SController::addMenu(Yii::t('app', 'Notifications'), '//notification/admin', 'list-alt', (Module::isManager()));
    } */
}
