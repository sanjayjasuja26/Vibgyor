<?php
/**
 * This is the template for generating a controller class within a module.
 */
/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\module\Generator */

echo "<?php\n";
?>

namespace <?= $generator->geSControllerNamespace() ?>;

use app\components\SController;

/**
* Default controller for the `<?= $generator->moduleID ?>` module
*/
class DefaulSController extends SController
{
/**
* Renders the index view for the module
* @return string
*/
public function actionIndex()
{
return $this->render('index');
}
}
