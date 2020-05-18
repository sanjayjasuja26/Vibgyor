<?php

use app\components\SGridView;
use yii\grid\GridView;
use yii\widgets\Pjax;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\Collegeevent $searchModel
 */

?>
<?php Pjax::begin(['id'=>'collegeevent-pjax-ajax-grid','enablePushState'=>false,'enableReplaceState'=>false]); ?>
    <?php echo SGridView::widget([
    	'id' => 'collegeevent-ajax-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table table-bordered'],
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],

            'id',
            'title',
            /* 'description:html',*/
            'start_on:datetime',
            'end_on:datetime',
            [
				'attribute' => 'college_id',
				'format'=>'raw',
				'value' => function ($data) { return $data->getRelatedDataLink('college_id');  },
				],
            [
			'attribute' => 'state_id','format'=>'raw','filter'=>isset($searchModel)?$searchModel->getStateOptions():null,
			'value' => function ($data) { return $data->getStateBadge();  },],
            /* ['attribute' => 'type_id','filter'=>isset($searchModel)?$searchModel->getTypeOptions():null,
			'value' => function ($data) { return $data->getType();  },],*/
            'created_on:datetime',
            /* 'updated_on:datetime',*/
            [
				'attribute' => 'created_by_id',
				'format'=>'raw',
				'value' => function ($data) { return $data->getRelatedDataLink('created_by_id');  },
				],

            ['class' => 'app\components\SActionColumn','header'=>'<a>Actions</a>'],
        ],
    ]); ?>
<?php Pjax::end(); ?>

