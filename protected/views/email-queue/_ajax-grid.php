<?php
use app\components\SGridView;
use yii\widgets\Pjax;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\EmailQueue $searchModel
 */

?>
<?php Pjax::begin(["enablePushState"=>false,"enableReplaceState"=>false,'id' => 'email-queue-pjax-grid']); ?>
    <?php
    
    echo SGridView::widget([
        'id' => 'email-queue-ajax-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-bordered'
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],
            
            'id',
            /* 'from_email:email',*/
             'to_email:email',
            /* 'message:html',*/
             'subject',
            /* 'date_published:datetime',*/
            /* 'last_attempt:datetime',*/
             'date_sent:datetime',
            /* 'attempts',*/
            /* [
			'attribute' => 'state_id','format'=>'raw','filter'=>isset($searchModel)?$searchModel->getStateOptions():null,
			'value' => function ($data) { return $data->getStateBadge();  },],*/
            /* 'model_id',*/
            /* 'model_type',*/

            [
                'class' => 'app\components\SActionColumn',
                'header' => "<a>".Yii::t("app",'Actions')."</a>"
            ]
        ]
    ]);
    ?>
<?php Pjax::end(); ?>

