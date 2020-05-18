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

namespace <?= $ns ?>;
use app\components\SController;
use app\components\SModule;
use app\models\User;
/**
* <?= $generator->moduleID ?> module definition class
*/
class <?= $className ?> extends SModule
{
const NAME = '<?= $generator->moduleID ?>';

public $controllerNamespace = '<?= $generator->geSControllerNamespace() ?>';

//public $defaultRoute = '<?= $generator->moduleID ?>';



public static function subNav()
{
return SController::addMenu(\Yii::t('app', '<?= Inflector::camel2words(Inflector::pluralize($generator->moduleID)) ?>'), '#', 'key ', Module::isAdmin(), [
// SController::addMenu(\Yii::t('app', 'Home'), '//<?= $generator->moduleID ?>', 'lock', Module::isAdmin()),
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
