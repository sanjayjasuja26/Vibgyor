<?php
use yii\helpers\Html;
use app\components\TActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Feed */
/* @var $form yii\widgets\ActiveForm */
?>
<header class="card-header">
                            <?php
                            // echo strtoupper(Yii::$app->controller->action->id); ?>
                        </header>


<?php
$form = TActiveForm::begin([
    /* 'layout' => 'horizontal', */
    'id' => 'feed-form',
    'options' => [
        'class' => 'row'
    ]
]);
?>

<div class="row">
	<div class="col-md-3">	 <?php
/* echo $form->field($model, 'content')->widget ( app\components\TRichTextEditor::className (), [ 'options' => [ 'rows' => 6 ],'preset' => 'basic' ] ); //$form->field($model, 'content')->textarea(['rows' => 6]); */
?>
	 		


		 <?php

echo $form->field($model, 'model_type')->textInput([
    'maxlength' => 128
])?>
	 </div>

	<div class="col-md-3">	
		 <?php

echo $form->field($model, 'model_id')->dropDownList($model->getModelOptions(), [
    'prompt' => ''
])?>
	 </div>
	<div class="col-md-3">			


		 <?php
/* echo $form->field($model, 'project_id')->dropDownList($model->getProjectOptions(), ['prompt' => '']) */
?>
	 		


		 <?php

echo $form->field($model, 'state_id')->dropDownList($model->getStateOptions(), [
    'prompt' => ''
])?>
	 	</div>
	<div class="col-md-3">	

		 <?php

echo $form->field($model, 'type_id')->dropDownList($model->getTypeOptions(), [
    'prompt' => ''
])?>
	 </div>
</div>

<div class="form-group clearfix">
	<div class="row">
		<div class="col-md-12 text-right">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id' => 'feed-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])?>
    </div>
	</div>
</div>

<?php

TActiveForm::end();
?>

