<?php
use app\components\SGridView;
use yii\widgets\Pjax;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Notice $searchModel
 */

?>
<?php

Pjax::begin([
    "enablePushState" => false,
    "enableReplaceState" => false,
    "id" => 'Notice-pjax-ajax-grid'
]);
?>
    <?php

    echo SGridView::widget([
        'id' => 'notice-ajax-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-bordered'
        ],
        'columns' => [
            // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],

            'id',
            'title',
            /* 'content:html',*/
            'model_type',
            'model_id',
            [
                'attribute' => 'state_id',
                'format' => 'raw',
                'filter' => isset($searchModel) ? $searchModel->getStateOptions() : null,
                'value' => function ($data) {
                    return $data->getStateBadge();
                }
            ],
            [
                'attribute' => 'type_id',
                'filter' => isset($searchModel) ? $searchModel->getTypeOptions() : null,
                'value' => function ($data) {
                    return $data->getType();
                }
            ],
            /* 'created_on:datetime',*/
            /* [
				'attribute' => 'created_by_id',
				'format'=>'raw',
				'value' => function ($data) { return $data->getRelatedDataLink('created_by_id');  },
				],*/

            [
                'class' => 'app\components\SActionColumn',
                'header' => "<a>".Yii::t("app",'Actions')."</a>"
            ]
        ]
    ]);
    ?>
<?php

Pjax::end();
?>

