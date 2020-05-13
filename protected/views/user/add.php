<?php
use yii\helpers\Inflector;

/**
 *
 * @copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 * @author : Shiv Charan Panjeta < shiv@toxsl.com >
 */

/* @var $this yii\web\View */
/* @var $model app\models\User */
$this->params ['breadcrumbs'] [] = [ 
		'label' => 'Users',
    'layout' => 'horizontal',
		'url' => [ 
				'user/index' 
		] 
];

$this->params ['breadcrumbs'] [] = Inflector::humanize ( Yii::$app->controller->action->id );
?>

<div class="wrapper">
	<div class="card">

		<div class="user-create">
	<?=  \app\components\PageHeader::widget(); ?>
</div>

	</div>

	<div class="content-section clearfix panel">
	
		<?= $this->render ( '_form', [ 'model' => $model ] )?></div>

</div>