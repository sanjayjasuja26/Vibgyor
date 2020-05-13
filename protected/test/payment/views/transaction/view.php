<?php
use app\modules\payment\models\Gateway;
use yii\helpers\VarDumper;

/* @var $this yii\web\View */

/* $this->title = $model->label() .' : ' . $model->name; */
$this->params ['breadcrumbs'] [] = [
    'label' => Yii::t ( 'app', 'Payment Transactions' ),
    'url' => [
        'index'
    ]
];
$this->params ['breadcrumbs'] [] = ( string ) $model;
?>

<div class="wrapper">
	<div class=" panel ">

		<div class="payment-transaction-view panel-body">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>



		</div>
	</div>

	<div class=" panel ">
		<div class=" panel-body ">
    <?php
				
				echo \app\components\TDetailView::widget ( [ 
						'id' => 'payment-transaction-detail-view',
						'model' => $model,
						'options' => [ 
								'class' => 'table table-bordered' 
						],
						'attributes' => [ 
								'id',
								'name',
								'email:email',
								[ 
										'attribute' => 'gateway_type',
										'format' => 'raw',
										'value' => function ($data) {
											$gateway = (new Gateway ())->getTypeOptions ();
											return isset ( $gateway [$data->gateway_type] ) ? $gateway [$data->gateway_type] : \Yii::t ( 'app', 'Not Defined' );
										} 
								],
								'payer_id',
								'transaction_id',
								'amount',
								'currency',
								[ 
										'attribute' => 'payment_status',
										'format' => 'raw',
										'value' => function ($data) {
											return $data->getPaymentStateBadge ();
										} 
								],
								'created_on:datetime' 
						] 
				] )?>
				<div class="row m-t-20 m-b-20">
				<div class="clearfix"></div>
				<hr style="border-color: #ccc">
				<h3 class="text-center"> <?= \Yii::t('app', 'Details') ?> </h3>
		<?php
		$response = $model->getPaymentResponse ();
		
		echo VarDumper::dumpAsString($response);
   
		?>
		<div class="clearfix"></div>
				<hr style="border-color: #ccc">
			</div>
		</div>
	</div>

</div>
