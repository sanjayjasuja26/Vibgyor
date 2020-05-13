<?php
/**
 *@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
 *@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
 */
namespace app\modules\installer\models;

use Yii;

/**
 * SelfTest is a helper class which checks all dependencies of the application.
 *
 * @since 1.0
 * @author Abhimanyu Saharan
 */
class CodeCheck
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
    public static function getRequiredDir()
    {
        return [
            'protected',
            'themes',
            'protected/commands',
            'protected/assets',
            'protected/components',
            'protected/config',
            'protected/controllers',
            'protected/db',
            'protected/mail',
            'protected/migrations',
            'protected/models',
            'protected/rules',
            'protected/views'
        ];
    }

    public static function getResults()
    {
        $projectroot = Yii::$app->basePath . '/../';
        $checks = [];
        foreach (self::getRequiredDir() as $path) {
            if (file_exists($projectroot . $path)) {
                $checks[] = [
                    'title' => 'Folder Exist :' . $path,
                    'state' => 'OK'
                ];
            } else {
                $checks[] = [
                    'title' => $path . ' Folder doesn\'t  Exist',
                    'state' => 'ERROR',
                    'hint' => $path . ' Folder Required'
                ];
            }
        }
        return $checks;
    }
}