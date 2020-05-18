<?php
namespace app\modules\payment;

use app\components\SModule;

/**
 * payment module definition class
 */
class Module extends SModule
{

    /**
     * @inheritdoc
     */
    public static $payConfig;

    public $controllerNamespace = 'app\modules\payment\controllers';

    public function init()
    {
        parent::init();
//         if (empty(\Yii::$app->db->getSchema()
//             ->getTableSchema('tbl_payment_transaction')
//             ->getColumn('description'))) {
//             \Yii::$app->db->createCommand("ALTER TABLE `tbl_payment_transaction` ADD `description` TEXT NULL DEFAULT NULL AFTER `email`;")->execute();
//         }
    }

    public static function dbFile()
    {
        return __DIR__ . '/db/install.sql';
    }
}
