<?php

use yii\helpers\Html;
use app\components\SActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Collegeevent */
/* @var $form yii\widgets\ActiveForm */
?>
<header class="panel-heading">
                            <?php echo strtoupper(Yii::$app->controller->action->id); ?>
                        </header>
<div class="panel-body">

    <?php 
$form = SActiveForm::begin([
					 'layout' => 'horizontal',
						'id'	=> 'collegeevent-form',
						]);
						
						
echo $form->errorSummary($model);	
?>





		 <?php echo $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>
	 		


		 <?php /*echo  $form->field($model, 'description')->widget ( app\components\SRichTextEditor::className (), [ 'options' => [ 'rows' => 6 ],'preset' => 'basic' ] ); //$form->field($model, 'description')->textarea(['rows' => 6]); */ ?>
	 		


		 <?php echo $form->field($model, 'start_on')->widget(yii\jui\DatePicker::className(),
			[
					//'dateFormat' => 'php:Y-m-d',
	 				'options' => [ 'class' => 'form-control' ],
	 				'clientOptions' =>
	 				[
			//'minDate' => 0,
			'changeMonth' => true,'changeYear' => true ] ]) ?>
	 		


		 <?php echo $form->field($model, 'end_on')->widget(yii\jui\DatePicker::className(),
			[
					//'dateFormat' => 'php:Y-m-d',
	 				'options' => [ 'class' => 'form-control' ],
	 				'clientOptions' =>
	 				[
			//'minDate' => 0,
			'changeMonth' => true,'changeYear' => true ] ]) ?>
	 		


		 <?php echo $form->field($model, 'college_id')->dropDownList($model->getCollegeOptions(), ['prompt' => '']) ?>
	 		


		 <?php echo $form->field($model, 'state_id')->dropDownList($model->getStateOptions(), ['prompt' => '']) ?>
	 		


		 <?php /*echo $form->field($model, 'type_id')->dropDownList($model->getTypeOptions(), ['prompt' => '']) */ ?>
	 		


	   <div class="form-group">
		<div
			class="col-md-6 col-md-offset-3 bottom-admin-button btn-space-bottom text-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id'=> 'collegeevent-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	</div>

    <?php SActiveForm::end(); ?>

</div>
