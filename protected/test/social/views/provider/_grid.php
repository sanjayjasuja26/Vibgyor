<?php
use app\components\SGridView;
use yii\helpers\Html;
use yii\helpers\Url;

use app\models\User;

use yii\grid\GridView;
use yii\widgets\Pjax;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Provider $searchModel
 */

?>
<?php if (User::isAdmin()) echo Html::a('','#',['class'=>'multiple-delete glyphicon glyphicon-trash','id'=>"bulk_delete_social-provider-grid"])?>
<?php Pjax::begin(['id'=>'social-provider-pjax-grid']); ?>
    <?php
				
				echo SGridView::widget ( [ 
						'id' => 'social-provider-grid-view',
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
								'title',
								[ 
										'attribute' => 'provider_type',
										'format' => 'raw',
										'filter' => isset ( $searchModel ) ? $searchModel->getClientOptions () : null,
										'value' => function ($data) {
											return $data->getClient ();
										} 
								],
								'client_id',
            /* 'client_secret_key',*/
            					[ 
										'attribute' => 'state_id',
										'format' => 'raw',
										'filter' => isset ( $searchModel ) ? $searchModel->getStateOptions () : null,
										'value' => function ($data) {
											return $data->getStateBadge ();
										} 
								],
								[ 
										'class' => 'app\components\SActionColumn',
										'header' => '<a>Actions</a>' 
								] 
						] 
				] );
				?>
<?php Pjax::end(); ?>
<script type="javascript/text"> 
$('#bulk_delete_social-provider-grid').click(function(e) {
	e.preventDefault();
	 var keys = $('#social-provider-grid-view').yiiGridView('getSelectedRows');

	 if ( keys != '' ) {
		var ok = confirm("Do you really want to delete these items?");

		if( ok ) {
			$.ajax({
				url  : '<?php echo Url::toRoute(['social-provider/mass','action'=>'delete','model'=>get_class($searchModel)])?>', 
				type : "POST",
				data : {
					ids : keys,
				},
				success : function( response ) {
					if ( response.status == "OK" ) {
						 $.pjax.reload({container: '#social-provider-pjax-grid'});
					}
				}
		     });
		}
	 } else {
		alert('Please select items to delete');
	 }
});

</script>

