<?php
use app\components\SGridView;
use yii\helpers\Html;
use yii\helpers\Url;

use app\models\User;

use app\modules\payment\models\Gateway;
use yii\widgets\Pjax;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\PaymentTransaction $searchModel
 */

?>
<?php if (User::isAdmin()) echo Html::a('','#',['class'=>'multiple-delete glyphicon glyphicon-trash','id'=>"bulk_delete_payment-transaction-grid"])?>
<?php Pjax::begin(['id'=>'payment-transaction-pjax-grid']); ?>
    <?php
				
				echo SGridView::widget ( [ 
						'id' => 'payment-transaction-grid-view',
						'dataProvider' => $dataProvider,
						'filterModel' => $searchModel,
						'tableOptions' => [ 
								'class' => 'table table-bordered' 
						],
						'columns' => [ 
								// ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],
								[ 
										'name' => 'check',
										'class' => 'yii\grid\CheckboxColumn',
										'visible' => User::isAdmin () 
								],
								'id',
								'amount',
								'currency',
								'transaction_id',
								[ 
										'attribute' => 'gateway_type',
										'format' => 'raw',
										'filter' => (new Gateway ())->getTypeOptions (),
										'value' => function ($data) {
											$gateway = (new Gateway ())->getTypeOptions ();
											return isset ( $gateway [$data->gateway_type] ) ? $gateway [$data->gateway_type] : \Yii::t ( 'app', 'Not Defined' );
										} 
								],
								[ 
										'attribute' => 'payment_status',
										'format' => 'raw',
										'filter' => isset ( $searchModel ) ? $searchModel->getPaymentState () : null,
										'value' => function ($data) {
											return $data->getPaymentStateBadge ();
										} 
								],
								'created_on:datetime',
								[ 
										'class' => 'app\components\SActionColumn',
										'header' => '<a>Actions</a>',
								        'template' => '{view}{delete}'
								] 
						] 
				] );
				?>
<?php Pjax::end(); ?>
<script> 
$('#bulk_delete_payment-transaction-grid').click(function(e) {
	e.preventDefault();
	 var keys = $('#payment-transaction-grid-view').yiiGridView('getSelectedRows');

	 if ( keys != '' ) {
		var ok = confirm("Do you really want to delete these items?");

		if( ok ) {
			$.ajax({
				url  : '<?php echo Url::toRoute(['transaction/mass','action'=>'delete','model'=>get_class($searchModel)])?>', 
				type : "POST",
				data : {
					ids : keys,
				},
				success : function( response ) {
					if ( response.status == "OK" ) {
						 $.pjax.reload({container: '#payment-transaction-pjax-grid'});
					}
				}
		     });
		}
	 } else {
		alert('Please select items to delete');
	 }
});

</script>

