<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
namespace app\modules\installer\models;

use app\modules\installer\helpers\InstallerHelper;
use YiiRequirementChecker;

class SystemCheck
{

    /**
     * Get Results of the Application SystemCheck.
     *
     * Fields
     * - title
     * - state (OK, WARNING or ERROR)
     * - hint
     *
     * @return Array
     */
    public static function getResults($module, $render = true)
    {
        require_once VENDOR_PATH . 'yiisoft/yii2/requirements/YiiRequirementChecker.php';
        $requirementsChecker = new YiiRequirementChecker();
        $requirements = [];
        $module->exts = array_unique(array_merge($module->exts, InstallerHelper::moduleExts()));
        foreach ($module->exts as $ext) {
            $requirements[] = array(
                'name' => $ext,
                'mandatory' => true,
                'condition' => extension_loaded($ext),
                'by' => 'Some application feature',
                'memo' => "PHP extension $ext required"
            );
        }
        $module->pkgs = array_unique(array_merge($module->pkgs, InstallerHelper::modulePkgs()));
        foreach ($module->pkgs as $pkg) {
            $requirements[] = array(
                'name' => $pkg,
                'mandatory' => true,
                'condition' => shell_exec('which ' . $pkg) != null ? true : false,
                'by' => 'Some application feature',
                'memo' => "Package extension $pkg required"
            );
        }
        
        $checks = $requirementsChecker->checkYii()
            ->check($requirements)
            ->getResult();
        if ($render)
            $requirementsChecker->render();
        return $checks;
    }
}