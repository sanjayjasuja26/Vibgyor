<?php


/* @var $this yii\web\View */
/* @var $model app\models\Provider */

/* $this->title = Yii::t('app', 'Add'); */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Social Providers'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Add');
?>

<div class="wrapper">
	<div class="panel">

		<div
			class="social-provider-create">
	<?=\app\components\PageHeader::widget();?>
</div>

	</div>
	
	
	
	
	
	

	<div class="content-section clearfix panel">

		<?=$this->render('_form', ['model' => $model])?></div>
</div>


