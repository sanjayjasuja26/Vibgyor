<?php
use app\components\SActiveForm;
use yii\helpers\Html;
?>
<div class="bg-bottom">
	<div class="icon-description1">
		<p class="line_h">
		<?= $description?></p>
	    <?php
    $form = SActiveForm::begin([
        'id' => 'cookies-actions-form'
    ]);
    echo Html::submitButton('Accept', array(
        'name' => 'accept',
        'value' => 'Accept',
        'class' => 'btn btn-success',
        'id' => 'information-form-submit'
    ));
    SActiveForm::end();
    ?>
   </div>
</div>