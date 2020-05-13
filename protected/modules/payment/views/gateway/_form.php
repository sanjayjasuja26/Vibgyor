<?php
use app\components\TActiveForm;
use app\modules\payment\models\Gateway;
use app\modules\payment\models\GatewaySetting;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentGateway */
/* @var $form yii\widgets\ActiveForm */
?>
<header class="panel-heading">
                            <?php echo strtoupper(Yii::$app->controller->action->id); ?>
                        </header>
<div class="panel-body">

    <?php
				$form = TActiveForm::begin ( [ 
				    'layout' => 'horizontal',
						'id' => 'payment-gateway-form' 
				] );
				?>

		 <?php echo $form->field($model, 'title')->textInput(['maxlength' => 255]) ?>

		 <?php echo $form->field($model, 'mode')->dropDownList($model->getModeOptions()) ?>

		 
		 <?php
			if (! $model->isNewRecord) {
				$gatewayFields = Gateway::gatewayForm ( $model->type_id );
				$gateWaySettings = $model->gatewaySettings ();
				if (! empty ( $gatewayFields )) {
					foreach ( $gatewayFields as $key => $field ) {
						if ($gateWaySettings) {
							if (is_array ( $field )) {
								$field ['value'] = isset ( $gateWaySettings [$key] ) ? $gateWaySettings [$key] : '';
							} else {
								$name = $field;
								$field = [ ];
								$field ['value'] = isset ( $gateWaySettings [$name] ) ? $gateWaySettings [$name] : '';
							}
						}
						
						echo GatewaySetting::generateField ( $key, $field );
					}
				}
			} else {
				echo $form->field ( $model, 'type_id' )->dropDownList ( $model->getTypeOptions () );
			}
			?>
			
			<?php echo $form->field($model, 'state_id')->dropDownList($model->getStateOptions()) ?>
	 		
	   <div class="form-group">
		<div
			class="col-md-6 col-md-offset-3 bottom-admin-button btn-space-bottom text-right">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Save') : Yii::t('app', 'Update'), ['id'=> 'payment-gateway-form-submit','class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>
	</div>

    <?php TActiveForm::end(); ?>

</div>
