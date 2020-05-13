<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
namespace app\modules\tugii;

class Module extends \yii\gii\Module
{

    public $controllerNamespace = 'app\modules\tugii\controllers';

    public function init()
    {
        parent::init();
        
        // custom initialization code goes here
    }

    protected function coreGenerators()
    {
        $local = [
            'tumodel' => [
                'class' => 'app\modules\tugii\generators\tumodel\Generator'
            ],
            'tucrud' => [
                'class' => 'app\modules\tugii\generators\tucrud\Generator'
            ],
            'tumigration' => [
                'class' => 'app\modules\tugii\generators\tumigration\Generator'
            ],
            'tuapi' => [
                'class' => 'app\modules\tugii\generators\tuapi\Generator'
            ],
            'tutest-case' => [
                'class' => 'app\modules\tugii\generators\tutestcase\Generator'
            ],
            'tumodule' => [
                'class' => 'app\modules\tugii\generators\tumodule\Generator'
            ]
        
        ];
        
        return array_merge($local, parent::coreGenerators());
    }
}
