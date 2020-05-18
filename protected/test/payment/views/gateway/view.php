<?php
use app\components\useraction\UserAction;
use yii\helpers\Inflector;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentGateway */

/* $this->title = $model->label() .' : ' . $model->title; */
$this->params ['breadcrumbs'] [] = [ 
		'label' => Yii::t ( 'app', 'Payment Gateways' ),
		'url' => [ 
				'index' 
		] 
];
$this->params ['breadcrumbs'] [] = ( string ) $model;
?>

<div class="wrapper">
	<div class=" panel ">

		<div class="payment-gateway-view panel-body">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>
		</div>
	</div>

	<div class=" panel ">
		<div class=" panel-body ">
    <?php
				
				echo \app\components\SDetailView::widget ( [ 
						'id' => 'payment-gateway-detail-view',
						'model' => $model,
						'options' => [ 
								'class' => 'table table-bordered' 
						],
						'attributes' => [ 
								'id',
								'title',
								[ 
										'attribute' => 'mode',
										'format' => 'raw',
										'value' => $model->getMode () 
								],
								[ 
										'attribute' => 'state_id',
										'format' => 'raw',
										'value' => $model->getStateBadge () 
								],
								[ 
										'attribute' => 'type_id',
										'value' => $model->getType () 
								],
								'created_on:datetime',
								'updated_on:datetime',
								[ 
										'attribute' => 'created_by_id',
										'format' => 'raw',
										'value' => $model->getRelatedDataLink ( 'created_by_id' ) 
								] 
						] 
				] )?>
				<div class="row m-t-20 m-b-20">
				<div class="clearfix"></div>
				<hr style="border-color: #ccc">
				<h3 class="text-center"> <?= \Yii::t('app', 'Details') ?> </h3>
				<?php
				$gateWaySettings = $model->gatewaySettings ();
				if ($gateWaySettings) {
					foreach ( $gateWaySettings as $key => $setting ) {
						?>
						<div class="col-md-12 m-t-10">
					<div class="col-md-2">
						<strong><?= Inflector::titleize ( $key ) ?></strong>
					</div>
					<div class="col-md-10"><?= $setting ?></div>
				</div>
					<?php
					}
				}
				?>
				<div class="clearfix"></div>
				<hr style="border-color: #ccc">
			</div>

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
