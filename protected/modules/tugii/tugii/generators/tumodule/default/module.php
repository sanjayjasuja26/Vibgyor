<?php
use yii\helpers\Inflector;

/**
 * This is the template for generating a module class file.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

$className = $generator->moduleClass;
$pos = strrpos($className, '\\');
$ns = ltrim(substr($className, 0, $pos), '\\');
$className = substr($className, $pos + 1);

echo "<?php\n";
?>
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
namespace <?= $ns ?>;
use app\components\TController;
use app\components\TModule;
use app\models\User;
/**
 * <?= $generator->moduleID ?> module definition class
 */
class <?= $className ?> extends TModule
{
    const NAME = '<?= $generator->moduleID ?>';

    public $controllerNamespace = '<?= $generator->getControllerNamespace() ?>';
	
	//public $defaultRoute = '<?= $generator->moduleID ?>';
	


    public static function subNav()
    {
        return TController::addMenu(\Yii::t('app', '<?= Inflector::camel2words(Inflector::pluralize($generator->moduleID)) ?>'), '#', 'key ', Module::isAdmin(), [
           // TController::addMenu(\Yii::t('app', 'Home'), '//<?= $generator->moduleID ?>', 'lock', Module::isAdmin()),
        ]);
    }
    /*
    public static function dbFile()
    {
        return __DIR__ . '/db/install.sql';
    }
    */
    
   /* public static function getRules()
    {
        return [
            
            '<?= $generator->moduleID ?>/<id:\d+>/<title>' => '<?= $generator->moduleID ?>/post/view',
           // '<?= $generator->moduleID ?>/post/<id:\d+>/<file>' => '<?= $generator->moduleID ?>/post/image',
           //'<?= $generator->moduleID ?>/category/<id:\d+>/<title>' => '<?= $generator->moduleID ?>/category/type'
        
        ];
    }
    */
    
}
