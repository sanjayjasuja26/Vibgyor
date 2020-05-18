<?php

use app\components\useraction\UserAction;
use app\modules\comment\widgets\CommentsWidget;
/* @var $this yii\web\View */
/* @var $model app\models\Course */

/*$this->title =  $model->label() .' : ' . $model->title; */
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = (string)$model;
?>

<div class="wrapper">
	<div class=" panel ">

		<div
			class="course-view panel-body">
			<?php echo  \app\components\PageHeader::widget(['model'=>$model]); ?>



		</div>
	</div>

	<div class=" panel ">
		<div class=" panel-body ">
    <?php echo \app\components\SDetailView::widget([
    	'id'	=> 'course-detail-view',
        'model' => $model,
        'options'=>['class'=>'table table-bordered'],
        'attributes' => [
            'id',
            'title',
            /*'description:html',*/
            [
			'attribute' => 'state_id',
			'format'=>'raw',
			'value' => $model->getStateBadge(),],
            [
			'attribute' => 'type_id',
			'value' => $model->getType(),
			],
            'created_on:datetime',
            'updated_on:datetime',
            [
			'attribute' => 'created_by_id',
			'format'=>'raw',
			'value' => $model->getRelatedDataLink('created_by_id'),
			],
        ],
    ]) ?>


<?php  echo $model->description;?>


		<?php				echo UserAction::widget ( [
						'model' => $model,
						'attribute' => 'state_id',
						'states' => $model->getStateOptions ()
				] );
				?>

		</div>
</div>
 


	<div class=" panel ">
				<div class=" panel-body ">
					<div
						class="course-panel">

<?php
//$this->context->startPanel();
//	$this->context->addPanel('Courseofferdbycolleges', 'courseofferdbycolleges', 'Courseofferdbycollege',$model);
//	$this->context->addPanel('Parentinfos', 'parentinfos', 'Parentinfo',$model);
//	//$this->context->addPanel('Studentinfos', 'studentinfos', 'Studentinfo',$model);
//
//$this->context->endPanel();
?>
				</div>
				</div>
			</div>
</div>
