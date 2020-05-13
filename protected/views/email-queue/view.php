<?php
use app\components\useraction\UserAction;

/* @var $this yii\web\View */
/* @var $model app\models\EmailQueue */

/* $this->title = $model->label() .' : ' . $model->id; */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Email Queues'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = (string) $model;
?>

<div class="wrapper">
	<div class="card ">

		<div class="email-queue-view card-body">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>

		</div>
	</div>

	<div class="card ">
		<div class="card-body ">
			<div class="email-queue">
    <?php
    
    echo \app\components\TDetailView::widget([
        'id' => 'email-queue-detail-view',
        'model' => $model,
        'options' => [
            'class' => 'table table-bordered'
        ],
        'attributes' => [
            'id',
            'subject',
            'from_email:email',
            'to_email:email',
            'date_sent:datetime',
          //  'date_published:datetime',
          //  'last_attempt:datetime',
           // 'attempts',
           /*  [
                'attribute' => 'state_id',
                'format' => 'raw',
                'value' => $model->getStateBadge()
            ], */
//             'model_id',
//             'model_type'
        ]
    ])?>

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

	<div class="card ">
		<div class="card-body ">

			<iframe src="<?php echo $model->getUrl('show')?>" width="80%"
				height="500px" frameborder="0"></iframe>
		</div>

	</div>
</div>
