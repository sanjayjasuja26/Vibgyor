<?php

namespace app\components;

use yii\console\Controller;

class TConsoleController extends Controller {

    public $dryRun = false;

    public function options($actionID) {
        return [
            'dryRun'
        ];
    }

    public function optionAliases() {
        return [
            'd' => 'dryRun'
        ];
    }

    public static function shellExec($strings) {
        echo shell_exec($strings);
    }

    public static function log($strings) {
        if (php_sapi_name() == "cli") {
            echo $strings . PHP_EOL;
        } else {
            \Yii::debug($strings);
        }
    }

}
