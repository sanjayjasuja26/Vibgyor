<?php

namespace app\components\commands;

use app\components\SConsoleController;
use Yii;
use yii\console\ExitCode;

class ModuleController extends SConsoleController {

    public $dryrun = false;
    public $module = null;

    public function options($actionID) {
        return [
            'dryrun',
            'module'
        ];
    }

    public function optionAliases() {
        return [
            'd' => 'dryrun',
            'm' => 'module'
        ];
    }

    public static function moduleList() {
        $config = include (DB_CONFIG_PATH . 'web.php');
        $configConsole = include (DB_CONFIG_PATH . 'console.php');

        $modules = array_merge(array_keys($config['modules']), array_keys($configConsole['modules']));

        return $modules;
    }

    public function actionMigrate() {
        self::log('Run migration on all modules');
        $modules = self::moduleList();
        if (!empty($modules)) {
            foreach ($modules as $module) {
                $path = Yii::$app->basePath . '/modules/' . $module . '/migrations';

                if (is_dir($path)) {
                    self::log('Run migration on  module:' . $path);
                    try {
                        Yii::$app->runAction("migrate", [
                            'migrationPath' => $path,
                            'interactive' => 0
                        ]);
                    } catch (\Exception $ex) {
                        self::log($ex->getMessage());
                        self::log($ex->getTraceAsString());
                    }
                    self::log('done:' . $path);
                }
            }
        }
        return ExitCode::OK;
    }

}
