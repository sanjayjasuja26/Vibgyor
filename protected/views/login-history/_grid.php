<?php
use app\components\TGridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use app\models\User;
use yii\helpers\Url;
use app\components\MassAction;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\LoginHistory $searchModel
 */

?>
<?php

echo MassAction::widget([
    'url' => Url::toRoute([
        '/login-history/mass'
    ]),
    'grid_id' => 'login-history-grid',
    'pjax_grid_id' => 'login-history-pjax-grid'
]);
?>
<div class="table table-responsive">
<?php

Pjax::begin([
    'id' => 'login-history-pjax-grid'
]);
echo TGridView::widget([
    'id' => 'login-history-grid',
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'tableOptions' => [
        'class' => 'table table-bordered'
    ],
    'columns' => [
        [
            'name' => 'check',
            'class' => 'yii\grid\CheckboxColumn',
            'visible' => User::isAdmin()
        ],
        // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],

        'id',
        [
            'attribute' => 'user_id',
            'format' => 'raw',
            'value' => function ($data) {
                return $data->getRelatedDataLink('user_id');
            }
        ],
        'user_ip',
        'user_agent',
            /* 'failer_reason',*/
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
            /* 'code',*/
            'created_on:datetime',

        [
            'class' => 'app\components\TActionColumn',
            'template' => '{view} {delete}',
            'header' => "<a>" . Yii::t("app", 'Actions') . "</a>",
            'showModal' => true
        ]
    ]
]);
?>
<?php

Pjax::end();
?>
</div>