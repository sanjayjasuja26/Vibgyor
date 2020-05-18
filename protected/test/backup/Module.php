<?php
namespace app\modules\backup;

use app\components\SModule;
use app\components\TController;

class Module extends SModule
{

    public $controllerNamespace = 'app\modules\backup\controllers';

    public $path;

    public $fileList;

    public function init()
    {
        parent::init();
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\backup\commands';
        }
        // custom initialization code goes here
    }

    public function getFileList()
    {
        return $this->fileList;
    }
    public static function subNav()
    {
        return TController::addMenu(\Yii::t('app', 'Backup'), '//backup', 'database', (Module::isAdmin()));
    }
}
