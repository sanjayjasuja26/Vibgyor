<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
use app\components\PageHeader;
use app\components\TDetailView;
use app\components\useraction\UserAction;
use app\models\User;
use app\modules\file\widgets\DocumentViewerWidget;
/* @var $this yii\web\View */
/* @var $model app\models\User */

$this->params['breadcrumbs'][] = [
    'label' => 'Users',
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = [
    'label' => $model->full_name
];

?>
<div class="wrapper">

	<div class="card">
		<?php

echo PageHeader::widget([
    'model' => $model
]);
?>
	</div>
	<div class="card">
		<div class="card-body ">
			<div class="row">
				<div class="col-md-2 pr0">
					<div class="profileimage">
    				<?=$model->displayImage($model->profile_file, ['class' => 'profile-pic'], 'default.png', true);?>
    			</div>
				</div>
				<div class="col-md-10">
			<?php
echo TDetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'full_name',
        'email:email',
        [
            'attribute' => 'role_id',
            'format' => 'raw',
            'value' => $model->getRoleOptions($model->role_id)
        ],

        'created_on:datetime'
    ]
])?>
			</div>
			</div>
		</div>
	</div>
	<div class="card">
		<div class="card-body">
				<?php
    if ((User::isAdmin()) && (\Yii::$app->user->id != $model->id)) {
        $actions = $model->getStateOptions();
        array_shift($actions);
        echo UserAction::widget([
            'model' => $model,
            'attribute' => 'state_id',
            'states' => $model->getStateOptions(),
            'allowed' => $actions
        ]);
    }
    ?>
			</div>
	</div>
	
	<?php

if ($model->role_id != User::ROLE_USER) {
    ?>
		<div class="card">
		<div class="card-body">
				<?php
    $this->context->startPanel();

    $this->context->addPanel('Pages', 'pages', 'Page', $model);

    $this->context->endPanel();
    ?>
			</div>
	</div>
	<?php
}
?>
<?=DocumentViewerWidget::widget(['model' => $model]);?>

</div>

