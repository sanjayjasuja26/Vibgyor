<?php
use app\components\TGridView;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MassAction;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Notice $searchModel
 */

?>
<?php

echo MassAction::widget([
    'url' => Url::toRoute([
        '/notice/mass'
    ]),
    'grid_id' => 'notice-grid',
    'pjax_grid_id' => 'notice-pjax-grid'
]);
?>
<div class="table table-responsive">
<?php

Pjax::begin([
    'id' => 'notice-pjax-grid'
]);
?>
    <?php

    echo TGridView::widget([
        'id' => 'notice-grid',
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
                'visible' => User::isAdmin()
            ],

            'id',
            'title',
            /* 'content:html',*/
            //'model_type',
							//	'model_id',
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
                'class' => 'app\components\TActionColumn',
                'header' => "<a>" . Yii::t("app", 'Actions') . "</a>"
            ]
        ]
    ]);
    ?>
<?php

Pjax::end();
?>
</div>