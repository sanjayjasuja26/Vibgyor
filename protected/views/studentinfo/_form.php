<?php

use yii\helpers\Html;
use app\components\SActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Studentinfo */
/* @var $form yii\widgets\ActiveForm */
?>
<header class="panel-heading">
                            <?php echo strtoupper(Yii::$app->controller->action->id); ?>
                        </header>
<div class="panel-body">

    <?php 
$form = SActiveForm::begin([
					 'layout' => 'horizontal',
						'id'	=> 'studentinfo-form',
						]);
						
						
echo $form->errorSummary($model);	
?>





		 <?php echo $form->field($model, 'user_id')->dropDownList($model->getUserOptions(), ['prompt' => '']) ?>
	 		


		 <?php echo $form->field($model, 'course_id')->dropDownList($model->getCourseOptions(), ['prompt' => '']) ?>
	 		


		 <?php echo $form->field($model, 'f_name')->textInput(['maxlength' => 255]) ?>
	 		


		 <?php echo $form->field($model, 'm_name')->textInput(['maxlength' => 255]) ?>
	 		


		 <?php echo $form->field($model, 'caste')->textInput() ?>
	 		


		 <?php echo $form->field($model, 'current_studies')->textInput() ?>
	 		


		 <?php echo $form->field($model, 'state_id')->dropDownList($model->getStateOptions(), ['prompt' => '']) ?>
	 		


		 <?php /*echo $form->field($model, 'type_id')->dropDownList($model->getTypeOptions(), ['prompt' => '']) */ ?>
	 		


	   <div class="form-group">
		<div
			class="col-md-6 col-md-offset-3 bottom-admin-button btn-space-bottom text-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id'=> 'studentinfo-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	</div>

    <?php SActiveForm::end(); ?>

</div>
