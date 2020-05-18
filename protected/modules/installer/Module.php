<?php

/**
 * @author      : Sanjay Jasuja < sanjayjasuja26@gmail.com >
 */

namespace app\modules\installer;

use app\components\SModule;

/**
 * install module definition class
 */
class Module extends SModule {

    public $exts = [];
    public $pkgs = [];
    public $sqlfile = null;
    public $layout = 'installer';

    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\installer\controllers';

    /**
     * @inheritdoc
     */
    public function init() {
        parent::init();

        if (\Yii::$app instanceof \yii\web\Application) {
            $this->layoutPath = __DIR__ . '/views/layouts/';
        }

        if ($this->sqlfile == null) {
            $this->sqlfile = [
                dirname(__FILE__) . '/db/install.sql'
            ];
        }
        if (\Yii::$app instanceof \yii\console\Application) {
            $this->controllerNamespace = 'app\modules\installer\command';
        }

        $this->sqlfile = is_array($this->sqlfile) ? $this->sqlfile : [
            $this->sqlfile
        ];
    }

}
