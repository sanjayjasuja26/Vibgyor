<?php
use app\components\TGridView;
use yii\helpers\Html;
use yii\helpers\Url;

use app\models\User;

use yii\widgets\Pjax;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\PaymentGateway $searchModel
 */

?>
<?php if (User::isAdmin()) echo Html::a('','#',['class'=>'multiple-delete glyphicon glyphicon-trash','id'=>"bulk_delete_payment-gateway-grid"])?>
<?php Pjax::begin(['id'=>'payment-gateway-pjax-grid']); ?>
    <?php
				
				echo TGridView::widget ( [ 
						'id' => 'payment-gateway-grid-view',
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
								'title',
								[ 
										'attribute' => 'mode',
										'format' => 'raw',
										'filter' => isset ( $searchModel ) ? $searchModel->getModeOptions () : null,
										'value' => function ($data) {
											return $data->getMode ();
										} 
								],
								[ 
										'attribute' => 'state_id',
										'format' => 'raw',
										'filter' => isset ( $searchModel ) ? $searchModel->getStateOptions () : null,
										'value' => function ($data) {
											return $data->getStateBadge ();
										} 
								],
								[ 
										'attribute' => 'type_id',
										'format' => 'raw',
										'filter' => isset ( $searchModel ) ? $searchModel->getTypeOptions () : null,
										'value' => function ($data) {
											return $data->getType ();
										} 
								],
								[ 
										'class' => 'app\components\TActionColumn',
										'header' => '<a>Actions</a>' 
								] 
						] 
				] );
				?>
<?php Pjax::end(); ?>
<script> 
$('#bulk_delete_payment-gateway-grid').click(function(e) {
	e.preventDefault();
	 var keys = $('#payment-gateway-grid-view').yiiGridView('getSelectedRows');

	 if ( keys != '' ) {
		var ok = confirm("Do you really want to delete these items?");

		if( ok ) {
			$.ajax({
				url  : '<?php echo Url::toRoute(['gateway/mass','action'=>'delete','model'=>get_class($searchModel)])?>', 
				type : "POST",
				data : {
					ids : keys,
				},
				success : function( response ) {
					if ( response.status == "OK" ) {
						 $.pjax.reload({container: '#payment-gateway-pjax-grid'});
					}
				}
		     });
		}
	 } else {
		alert('Please select items to delete');
	 }
});

</script>

