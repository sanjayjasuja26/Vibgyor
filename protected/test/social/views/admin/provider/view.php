<?php
use app\components\useraction\UserAction;

/* @var $this yii\web\View */
/* @var $model app\models\Provider */

/* $this->title = $model->label() .' : ' . $model->title; */
$this->params ['breadcrumbs'] [] = [ 
		'label' => Yii::t ( 'app', 'Social Providers' ),
		'url' => [ 
				'index' 
		] 
];
$this->params ['breadcrumbs'] [] = ( string ) $model;
?>

<div class="wrapper">
	<div class=" panel ">

		<div class="social-provider-view panel-body">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>
		</div>
	</div>

	<div class=" panel ">
		<div class=" panel-body ">
    <?php
				
				echo \app\components\SDetailView::widget ( [ 
						'id' => 'social-provider-detail-view',
						'model' => $model,
						'options' => [ 
								'class' => 'table table-bordered' 
						],
						'attributes' => [ 
								'title',
								/* [ 
										'attribute' => 'provider_type',
										'value' => $model->getClient () 
								], */
								'client_id',
								'client_secret_key',
								
								'created_on:datetime',
								'updated_on:datetime' 
						] 
				] )?>


<?php  ?>


		<?php
		
		echo UserAction::widget ( [ 
				'model' => $model,
				'attribute' => 'state_id',
				'states' => $model->getStateOptions () 
		] );
		?>

		</div>
	</div>

</div>
