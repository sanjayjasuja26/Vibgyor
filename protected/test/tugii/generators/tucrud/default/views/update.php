<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams ();

echo "<?php\n";
?>


/* @var $this yii\web\View */
/* @var $model <?=ltrim ( $generator->modelClass, '\\' )?> */

/* $this->title = <?=$generator->generateString ( 'Update {modelClass}: ', [ 'modelClass' => Inflector::camel2words ( StringHelper::basename ( $generator->modelClass ) ) ] )?> . ' ' . $model-><?=$generator->getNameAttribute ()?>; */
$this->params['breadcrumbs'][] = ['label' => <?=$generator->generateString ( Inflector::pluralize ( Inflector::camel2words ( StringHelper::basename ( $generator->modelClass ) ) ) )?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?=$generator->getNameAttribute ()?>, 'url' => ['view', <?=$urlParams?>]];
$this->params['breadcrumbs'][] = <?=$generator->generateString ( 'Update' )?>;
?>
<div class="wrapper">
	<div class=" panel ">
		<div
			class="<?=Inflector::camel2id ( StringHelper::basename ( $generator->modelClass ) )?>-update">
	<?="<?= "?> \app\components\PageHeader::widget(['model' => $model]); ?>
	</div>
	</div>


	<div class="content-section clearfix panel">
		<?="<?= "?>$this->render ( '_form', [ 'model' => $model ] )?></div>
</div>

