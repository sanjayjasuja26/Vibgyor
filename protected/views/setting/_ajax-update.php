<?php
use app\components\TActiveForm;
use yii\helpers\Html;
use app\models\Setting;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
$i = 1;
$defaultConfig = (Setting::getDefaultConfig());
?>
<div class="panel-body">
    <?php
    $form = TActiveForm::begin([
        'layout' => 'horizontal',
        'id' => 'setting-update-form',
        'action' => [
            '/setting/update',
            'id' => $model->id
        ],
        'enableAjaxValidation' => true,
        'enableClientValidation' => false
    ]);
    ?>	
		
	<div class="col-md-12">
		<div class="form-group">
			<div class="col-md-12">
				<label>Configration Title</label>
			</div>
			<div class="col-md-12">
				<input type="text" id="setting-title" class="form-control"
					name="Setting[title]" placeholder="Value"
					value="<?=\Yii::$app->settings->$key->title?>">
			</div>
		</div>
	</div>

	<div id="configValueContainer"> 
	   <?php
    foreach (\Yii::$app->settings->$key->asArray as $configKey => $configDetail) {
        if (isset($configDetail["display"]) && empty($configDetail['display'])) {} else {
            ?>
				<div class="col-md-12 config-value single-config-<?=$i?>">
			<div class="form-group">
				<div class="col-md-12">
							<?php

if ((YII_ENV == "dev") && (! array_key_exists($key, $defaultConfig))) {
                ?>
								<input type="text" required id="setting-keyName-<?=$i?>"
						class="form-control settingKey" name="Setting[keyName][<?=$i?>]"
						placeholder="Name" value="<?=$configKey?>">
					<p class="text-danger"><?=\Yii::t("app", "Only Character or Underscore is allowed.")?></p>
							<?php

} else {
                ?>
								<label><?=$configKey?></label>
							<?php

}
            ?>
						</div>
				<div class="col-md-12">
							<?php

            if (array_key_exists($key, $defaultConfig)) {
                echo Setting::generateField($configKey, $configDetail);
                ?>
								
									<?php

} else {
                ?>
								<input type="text" id="setting-keyValue-<?=$i?>" disabled
						class="form-control" name="Setting[keyValue][<?=$i?>]"
						placeholder="Value" value="<?=$configDetail['value']?>">
							<?php

}
            ?>
						</div>
			</div>
		</div>					
	  <?php

$i ++;
        }
    }
    ?>
	  </div>

	<div class="form-group">
		<div class="col-md-12 text-right btn-space-bottom">
       	 	<?=Html::submitButton(Yii::t('app', 'Update'), ['id' => 'bulk-message-form-submit','class' => 'btn btn-success'])?>
    	</div>
	</div>
    <?php

TActiveForm::end();
    ?>
</div>