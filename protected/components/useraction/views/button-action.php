<?php
use app\components\TActiveForm;
use yii\helpers\Html;
?>
<div class="clearfix"></div>
<div class="form">

    <?php $form = TActiveForm::begin(['id' => 'user-actions-form',]); ?>
		<?= $title?>
		<div class="btn-group pull-right">


	<?php

foreach ($allowed as $id => $act) {

    if ($id != $model->{$attribute}) {
        $button = $buttons[$id];
        echo '';
        echo Html::submitButton($button, array(
            'name' => 'workflow',
            'value' => $id,
            'class' => 'btn ' . $this->context->getButtonColor($button)
        ));
        echo '';
    }
}

?>
	
	</div>
<?php TActiveForm::end(); ?>
</div>