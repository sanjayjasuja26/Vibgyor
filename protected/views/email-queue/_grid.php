<?php
use app\components\SGridView;
use yii\widgets\Pjax;
use app\models\User;
use yii\helpers\Url;
use app\components\MassAction;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\EmailQueue $searchModel
 */

?>
<?php

echo MassAction::widget([
    'url' => Url::toRoute([
        '/email-queue/mass'
    ]),
    'grid_id' => 'email-queue-grid',
    'pjax_grid_id' => 'email-queue-pjax-grid'
]);
?>
<div class="table table-responsive">

<?php

Pjax::begin([
    "enablePushState" => false,
    "enableReplaceState" => false,
    'id' => 'email-queue-pjax-grid'
]);
?>
    <?php

    echo SGridView::widget([
        'id' => 'email-queue-grid',
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
            /* 'from_email:email',*/
            'to_email:email',
            /* 'message:html',*/
            'subject',
            /* 'date_published:datetime',*/
            /* 'last_attempt:datetime',*/
             'date_sent:datetime',
            /* 'attempts',*/
             [
                'attribute' => 'state_id',
                'format' => 'raw',
                'filter' => isset($searchModel) ? $searchModel->getStateOptions() : null,
                'value' => function ($data) {
                    return $data->getStateBadge();
                }
            ],
            /* 'model_id',*/
            /* 'model_type',*/

            [
                'class' => 'app\components\SActionColumn',
                'template' => '{view}{delete}',
                'header' => "<a>" . Yii::t("app", 'Actions') . "</a>"
            ]
        ]
    ]);
    ?>
<?php

Pjax::end();
?>

</div>
