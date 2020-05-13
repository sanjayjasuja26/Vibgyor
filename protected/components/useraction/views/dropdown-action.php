<?php
use app\components\TActiveForm;
use yii\helpers\Html;
?>



<div class="col-md-12">
	
    <?php $form = TActiveForm::begin(['id' => 'user-actions-form',]); ?>
<div class="col-md-12 panel-body bg-grey">
		<div class=" ">
			<div class="col-md-2" style="margin-top: 10px;">
			<?= $title?>
			</div>
			<div class="col-md-9">
		<?= Html::dropDownList('workflow',null,array_combine($allowed,$allowed),['class' => 'form-control']);?>
	</div>
			<div class="col-md-1">
		<?= Html::submitButton(Yii::t('app', 'Go') , ['id'=> 'project-form-submit','class' => 'btn btn-success']);?>
	</div>
	<?php

/*
 * foreach ( $allowed as $id => $act ) {
 *
 * if ($id != $model->{$attribute}) {
 *
 * echo '';
 * echo Html::submitButton ( $act, array (
 * 'name' => 'workflow',
 * 'value' => $act,
 * 'class' => 'btn ' . $this->context->getButtonColor ( $act )
 * ) );
 * echo '';
 * }
 * }
 */

?>
	</div>
	</div>
<?php TActiveForm::end(); ?>

<?php if(\Yii::$app->session->hasFlash('user-action')): ?>
<div class="col-md-6">
		<div class="alert alert-info ">
			<div class="flash-success alert-link">
	<?php echo \Yii::$app->session->getFlash('user-action'); ?>
		</div>
		</div>
	</div>
<?php endif;?>
</div>