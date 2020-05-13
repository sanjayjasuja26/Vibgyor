<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/
namespace app\components;

use app\models\User;
use Yii;
use yii\helpers\Html;
use app\modules\rbac\models\Rule;

/**
 * Tmodule definition class
 */
class TModule extends \yii\base\Module
{

    public $allowGlobalAccess = false;

    const NAME = '';

    public static function self()
    {
        return Yii::$app->getModule(get_called_class()::NAME);
    }

    public static function isAdmin()
    {
        $module = Yii::$app->getModule(get_called_class()::NAME);
        if ($module)
            return User::isAdmin() || $module->allowGlobalAccess;
        return User::isAdmin();
    }

    public static function isManager()
    {
        $module = Yii::$app->getModule(get_called_class()::NAME);
        if ($module)
            return User::isManager() || $module->allowGlobalAccess;
        return User::isManager();
    }

    public static function logo()
    {
        return Html::img(base64_decode('aHR0cDovL3lpaS5ndXJ1L3lpaTI='), [
            'class' => 'img-responsive',
            'alt' => 'Image'
        ]);
    }

    /**
     *
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\\' . $this->id . '\commands';
        }
        if (\Yii::$app instanceof \yii\web\Application) {
            $this->layoutPath = Yii::$app->view->theme->basePath . '/views/layouts/';
        }
    }

    public static function dbFile()
    {
        return [];
    }

    public static function getRules()
    {
        return [];
    }

    public static function getExts()
    {
        return [];
    }

    public static function getPkgs()
    {
        return [];
    }

    public static function isAllowed($moduleName, $link = null)
    {
        return Yii::$app->user->canRoute($moduleName, '*', true, false);
    }

    public static function addmenu($label, $link, $icon, $visible = null, $list = null)
    {   
        $route = $link;
        if (preg_match('/\/\/(?<module>\w+)\/(?<route>.*)/i', $link, $matches)) {
            $module = $matches['module'];
        } else if (preg_match('/^(?!\/\/)(?<route>.*)/i', $link, $matches)) {
            $module = get_called_class()::NAME;
        } else {
            $module = get_called_class()::NAME;
        }
 
        $visible = Yii::$app->user->canRoute($module, $route, true, $visible);

        return TController::addmenu($label, $link, $icon, $visible, $list);
    }
}
