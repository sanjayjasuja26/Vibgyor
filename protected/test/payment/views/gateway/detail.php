<?php
use app\components\TActiveForm;
use app\modules\payment\models\Gateway;
use app\modules\payment\models\GatewaySetting;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PaymentGateway */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wrapper">
	<div class="panel">

		<div class="payment-gateway-create">
	<?=  \app\components\PageHeader::widget(); ?>
</div>

	</div>

	<div class="content-section clearfix panel">

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
				
				<?php
				
				$gatewayFields = Gateway::gatewayForm ( $model->type_id );
				
				if (! empty ( $gatewayFields )) {
					foreach ( $gatewayFields as $key => $field ) {
						echo GatewaySetting::generateField ( $key, $field );
					}
				}
				?>


			<div class="form-group">
				<div
					class="col-md-6 col-md-offset-3 bottom-admin-button btn-space-bottom text-right">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['id'=> 'payment-gateway-form-submit','class' => 'btn btn-primary']) ?>
    </div>
			</div>

    <?php TActiveForm::end(); ?>

</div>


	</div>
</div>
