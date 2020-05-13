<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
namespace app\modules\installer\command;


class RemoveController extends InstallController
{

    public function actionIndex()
    {
        return $this->actionRemove();
    }
    public function actionModule()
    {
        return $this->actionRemoveModule();
    }
}