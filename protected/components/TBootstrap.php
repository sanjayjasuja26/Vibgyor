<?php

namespace app\components;

use Yii;
use yii\base\BootstrapInterface;
use yii\helpers\VarDumper;

/**
 * Class ModuleBootstrap
 *
 * @package app\extensions
 */
class SBootstrap implements BootstrapInterface {

    public function bootstrap($oApplication) {
        $aModuleList = $oApplication->getModules();

        foreach ($aModuleList as $sKey => $aModule) {
            if (is_array($aModule) && strpos($aModule['class'], 'app\modules') === 0 && is_subclass_of($aModule['class'], SModule::class)) {
                $className = $aModule['class'];

                $sFilePathConfig = $className::getRules();
                if (!empty($sFilePathConfig)) {
                    Yii::trace($sKey . " :adding rules : " . VarDumper::dumpAsString($sFilePathConfig));
                    $oApplication->geSUrlManager()->addRules($sFilePathConfig, false);
                }
            }
        }
    }

}
