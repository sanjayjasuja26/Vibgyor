<?php
use app\components\TActiveForm;
use yii\helpers\Html;
?>
<div class="bg-bottom">
	<div class="icon-description1">
		<p class="line_h">
		<?= $description?></p>
	    <?php
    $form = TActiveForm::begin([
        'id' => 'cookies-actions-form'
    ]);
    echo Html::submitButton('Accept', array(
        'name' => 'accept',
        'value' => 'Accept',
        'class' => 'btn btn-success',
        'id' => 'information-form-submit'
    ));
    TActiveForm::end();
    ?>
   </div>
</div>