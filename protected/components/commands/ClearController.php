<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
 */
namespace app\components\commands;

use app\base\TDefaultData;
use app\models\EmailQueue;
use Yii;
use yii\base\Exception;
use yii\helpers\FileHelper;
use app\components\TConsoleController;
use app\modules\file\models\File;

class ClearController extends TConsoleController
{

    public function actionChar()
    {
        self::log('actionChar' );

        foreach (\Yii::$app->db->schema->tableNames as $table) {
            
            self::log("character " . $table);
            \Yii::$app->db->createCommand()
            ->setSql("ALTER TABLE
                $table
                CONVERT TO CHARACTER SET utf8mb4
                COLLATE utf8mb4_unicode_ci")
                ->execute();
        }
    }

    public function actionIndex()
    {
        $this->actionRuntime();
        $this->actionAsset();
    }

    /**
     * Add default data
     */
    public function actionDefault()
    {
        $this->actionRuntime();
        $this->actionAsset();
        $this->actionUploads();
        $this->actionDb(1);
        TDefaultData::data();
    }

    public static function actionRuntime($delete = false)
    {
        $dir = Yii::getAlias('@runtime');
        if (is_dir($dir)) {
            if ($delete) {
                FileHelper::removeDirectory($dir);
                return;
            }

            $objects = scandir($dir);

            $objects = FileHelper::findFiles($dir);
            foreach ($objects as $object) {
                if (! unlink($object)) {
                    self::log('Unlink Error:' . $object);
                }
            }
            reset($objects);

            self::log('Runtime cleaned');
        }
    }

    public function actionAsset()
    {
        $assetsDirs = DB_CONFIG_PATH . '/../../assets/';
        if (is_dir($assetsDirs)) {

            FileHelper::removeDirectory($assetsDirs);
        }
        self::log('Assets cleaned');
    }

    public function actionUploads()
    {
        $uploadDirs = UPLOAD_PATH;
        if (is_dir($uploadDirs)) {

            FileHelper::removeDirectory($uploadDirs);
        }
        self::log('Uploads cleaned');
    }

    public function actionDb($dontSkip = 0)
    {
        self::log('clean db dontSkip:' . $dontSkip);

        if (YII_ENV == 'prod')
            return;

        $skip_tables = [
            'tbl_user_role',
            'tbl_user'
        ];
        \Yii::$app->db->createCommand()
            ->checkIntegrity(false)
            ->execute();

        foreach (\Yii::$app->db->schema->tableNames as $table) {
            if (! $dontSkip && in_array($table, $skip_tables)) {
                continue;
            }
            self::log("Cleaning " . $table);
            \Yii::$app->db->createCommand()
                ->truncateTable($table)
                ->execute();
        }
        \Yii::$app->db->createCommand()
            ->checkIntegrity(true)
            ->execute();

        FileHelper::removeDirectory(UPLOAD_PATH);
    }

    public function actionEmailQueue($m = 12)
    {
        $query = EmailQueue::find()->where([
            'state_id' => EmailQueue::STATE_SENT
        ])
            ->andWhere([
            '<',
            'date_sent',
            date('Y-m-d H:i:s', strtotime("-$m months"))
        ])
            ->orderBy('id asc');

        EmailQueue::log("Cleaning up emails : " . $query->count());
        foreach ($query->each() as $email) {
            EmailQueue::log("Deleting  email :" . $email->id . ' - ' . $email);
            try {
                $email->delete();
            } catch (Exception $e) {
                echo $e->getMessage();
                echo $e->getTraceAsString();
            }
        }
        if ($m == 0) {
            EmailQueue::truncate();
        }
    }

    public function actionFiles($id = 0, $limit = 0)
    {
        $query = File::find()->where([
            '>',
            'id',
            $id
        ])->orderBy('id asc');

        if ($limit > 0) {
            $query->limit($limit);
        }
        File::log("Cleaning up files : " . $query->count());
        foreach ($query->each() as $file) {

            try {

                $file->rename();
            } catch (Exception $e) {
                echo $e->getMessage();
                echo $e->getTraceAsString();
            }
        }
    }
}

