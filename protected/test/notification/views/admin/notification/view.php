<?php
use app\components\useraction\UserAction;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\notification\models\Notification */

/* $this->title = $model->label() .' : ' . $model->title; */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Notifications'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = (string) $model;
?>

<div class="wrapper">
	<div class=" panel ">

		<div class="notification-view panel-body">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>



		</div>
	</div>

	<div class=" panel ">
		<div class=" panel-body ">
    <?php
    
    echo \app\components\TDetailView::widget([
        'id' => 'notification-detail-view',
        'model' => $model,
        'options' => [
            'class' => 'table table-bordered'
        ],
        'attributes' => [
            'id',
            'title',
            /*'description:html',*/
            /* 'model_id',
            'model_type', */
            [
                'attribute' => 'is_read',
                'format' => 'raw',
                'value' => $model->getIsReadBadge()
            ],
            [
                'attribute' => 'state_id',
                'format' => 'raw',
                'value' => $model->getStateBadge()
            ],
            // [
            // 'attribute' => 'type_id',
            // 'value' => $model->getType()
            // ],
            'created_on:datetime',
            [
                'attribute' => 'to_user_id',
                'value' => function ($model) {
                    if (empty($model->to_user_id)) {
                        return "Not Set";
                    } else {
                        return isset($model->toUser) ? $model->toUser->full_name : "Not Set";
                    }
                }
            ],
            [
                'attribute' => 'created_by_id',
                'value' => function ($model) {
                    if (empty($model->created_by_id)) {
                        return "Not Set";
                    } else {
                        return isset($model->createdBy) ? $model->createdBy->full_name : "Not Set";
                    }
                }
            ]
        ]
    ])?>


<?php  echo $model->description;?>


		<?php
if(User::isManager())
echo UserAction::widget([
    'model' => $model,
    'attribute' => 'state_id',
    'states' => $model->getStateOptions()
]);
?>

		</div>
	</div>
</div>
