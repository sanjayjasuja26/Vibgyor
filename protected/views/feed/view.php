<?php
use app\components\useraction\UserAction;
use app\modules\comment\widgets\CommentsWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Feed */

/* $this->title = $model->label() .' : ' . $model->id; */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Feeds'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = (string) $model;
?>

<div class="wrapper">
	<div class="card panel-info ">
		<div class="card-header">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>

		</div>
		<div class="card-body ">
    <?php

    echo \app\components\TDetailView::widget([
        'id' => 'feed-detail-view',
        'model' => $model,
        'options' => [
            'class' => 'table table-bordered'
        ],
        'attributes' => [
            'id',
            /*'content:html',*/
            'model_type',
            'model_id',
            [
                'attribute' => 'project_id',
                'format' => 'raw',
                'value' => $model->getRelatedDataLink('project_id')
            ],
            [
                'attribute' => 'state_id',
                'format' => 'raw',
                'value' => $model->getStateBadge()
            ],
            [
                'attribute' => 'type_id',
                'value' => $model->getType()
            ],
            'created_on:datetime',
            'updated_on:datetime',
            [
                'attribute' => 'created_by_id',
                'format' => 'raw',
                'value' => $model->getRelatedDataLink('created_by_id')
            ]
        ]
    ])?>


<?php  echo $model->content;?>
<div class="card">
				<div class="card-body ">
					<div class="col-md-8 col-md-offset-2">


		<?php

echo UserAction::widget([
    'model' => $model,
    'attribute' => 'state_id',
    'states' => $model->getStateOptions()
]);
?>
				</div>

				</div>
			</div>

		</div>
	</div>
 
	
<?php echo CommentsWidget::widget(['model'=>$model]); ?>
</div>
