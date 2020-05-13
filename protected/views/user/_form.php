    <?php
				/**
				 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
				 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
				 */
				use app\components\TActiveForm;
				use yii\helpers\Html;
				use yii\helpers\Url;
				use yii\helpers\ArrayHelper;
				use yii\data\Pagination;
				/* @var $this yii\web\View */
				/* @var $model app\models\User */
				/* @var $form yii\widgets\ActiveForm */
				?>

<div class="card-body">
    <?php

				$form = TActiveForm::begin([
					'id' => 'user-form',

					'enableClientValidation' => true,
					'options' => [
						'class' => 'row'
					]
				]);
				?>
		<div class="col-lg-6 col-md-12 col-sm-12">
	<?=$form->field($model, 'full_name')->textInput(['maxlength' => 55])?>
	</div>
	<div class="col-lg-6 col-md-12 col-sm-12">
 	<?=$form->field($model, 'email')->textInput(['maxlength' => 128])?>

   </div>
		
   <?php

			if (Yii::$app->controller->action->id != 'update')
			{
				?>
    <div class="col-lg-6 col-md-12 col-sm-12">
		<?=$form->field($model, 'password')->passwordInput(['maxlength' => true])?>
	</div>
	<div class="col-lg-6 col-md-12 col-sm-12">
		<?=$form->field($model, 'confirm_password')->passwordInput(['maxlength' => true])?>
	</div>
	
     <?php
			}
			?>
		<div class="col-lg-6 col-md-12 col-sm-12">
	 <?=$form->field($model, 'contact_no')->textInput(['maxlength' => 11])?>
</div>
<div class="col-lg-6 col-md-12 col-sm-12">
	 <?=$form->field($model, 'profile_file')->fileInput()?>
</div>
	<div class="col-lg-6 col-md-12 col-sm-12">
    
	<?php
	if ((\Yii::$app->hasModule('file')) && (! $model->isNewRecord))
	{
	    $path = '//../modules/file/views/file/_upload';
		echo $this->render($path, [
			'model' => $model,
		   
			'options' => [
				'multiple' => true
			], // optional
		    'pluginOptions' => [
		        'maxFileSize' => 2000
		    ],
			'uploadExtraData' => [
				'public' => true
			], // uploaded files are automatically public (default is: protected). optional.
			'target_url' => Url::to([
				'user/view',
				'id' => $model->id
			]) // optional
		]);
	}

	?>
	
	</div>
	
	<div class="col-md-12 bottom-admin-button btn-space-bottom">
		<div class="form-group text-right">
        <?=Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['id' => 'user-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success'])?>
    </div>
	</div>

    <?php

				TActiveForm::end();
				?>

</div>