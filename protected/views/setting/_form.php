<?php
use yii\helpers\Html;
use app\components\TActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Setting */
/* @var $form yii\widgets\ActiveForm */
?>
<header class="card-header"><?php echo strtoupper(Yii::$app->controller->action->id); ?></header>
<div class="card-body">

    <?php
    $form = TActiveForm::begin([
        'layout' => 'horizontal',
        'id' => 'setting-form'
    ]);
    ?>
<div class="form-group">
		<div class="col-sm-3"></div>
		<div class="col-sm-6">
			<p class="text-danger"><?= \Yii::t("app", "Only Character or Under score is allowed for key.") ?></p>
		</div>
	</div>
		 <?php echo $form->field($model, 'key')->textInput(['maxlength' => 255, 'class' => 'settingKey'])?>

		 <?php echo $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

	   <div class="form-group">
		<div
			class="col-md-6 col-md-offset-3 bottom-admin-button btn-space-bottom text-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id'=> 'setting-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	</div>

    <?php TActiveForm::end(); ?>

</div>
<script>
$(document).on("keyup", ".settingKey",function (e) {
	var check = /^[_a-zA-Z]*$/;
	if (!check.test($(this).val())) {
		alert("<?= \Yii::t('app', 'Only Character or Underscore is allowed.'); ?>");
		$(this).val("");
		e.preventDefault();
	}
});
</script>