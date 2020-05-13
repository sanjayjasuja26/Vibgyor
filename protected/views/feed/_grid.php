<?php
use app\components\MassAction;
use app\components\TGridView;
use app\models\User;
use yii\helpers\Url;
use yii\widgets\Pjax;
/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Feed $searchModel
 */
?>
<?php

echo MassAction::widget([
    'url' => Url::toRoute([
        '/feed/mass'
    ]),
    'grid_id' => 'feed-grid',
    'pjax_grid_id' => 'feed-pjax-grid'
]);
?>
<div class="table table-responsive">
<?php

Pjax::begin([
    'id' => 'feed-pjax-grid'
]);
?>
    <?php

    echo TGridView::widget([
        'id' => 'feed-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => [
            'class' => 'table table-bordered table-striped'
        ],
        'columns' => [
            [
                'name' => 'check',
                'class' => 'yii\grid\CheckboxColumn',
                'visible' => User::isAdmin()
            ],

            'id',
            'content:html',
            /* [
				'attribute' => 'project_id',
				'format'=>'raw',
				'value' => function ($data) { return $data->getRelatedDataLink('project_id');  },
				],*/
            /* ['attribute' => 'type_id','filter'=>isset($searchModel)?$searchModel->getTypeOptions():null,
			'value' => function ($data) { return $data->getType();  },], */
            'created_on:datetime',
            'updated_on:datetime',
            [
                'attribute' => 'created_by_id',
                'format' => 'raw',
                'value' => function ($data) {
                    return $data->getRelatedDataLink('created_by_id');
                }
            ],

            [
                'class' => 'app\components\TActionColumn',
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