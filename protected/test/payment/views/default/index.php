<?php
use app\components\TActiveForm;
use app\modules\payment\assets\PaymentAsset;
use app\modules\payment\components\Currency;
use app\modules\payment\models\Payment;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
PaymentAsset::register($this);
$model = new Payment();

if (! empty($amount)) {
    $model->amount = $amount;
}
if (! empty($currency)) {
    $model->currency = $currency;
}
?>
<section class="cx-section contact-section gray-bg">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2">
				<h3 class="text-center title" data-wow-duration="2s">
					<?= \Yii::$app->params['company'] ?>
				</h3>
				<?php
    $form = TActiveForm::begin([
        /*
         * 'action' => [
         * '/payment/paypal/paynow'
         * ],
         */
        'enableAjaxValidation' => false,
        'enableClientValidation' => true
    ]);
    ?>
					<div class="col-md-6">
						<?= $form->field ( $model, 'amount' )->textInput ( [ 'class' => 'text-right form-control', 'type' => 'number', 'placeholder' => 'Amount' ] )?>
					</div>
				<div class="col-md-6">
						<?= $form->field ( $model, 'currency' )->dropDownList( Currency::getCurrencyList() ); ?>
					</div>
				<div class="col-md-12">
						<?= $form->field ( $model, 'description' )->textarea(); ?>
					</div>
				<div class="form-group">
					<div class="col-md-12 text-right">
					<?php
    foreach ($gateways as $gateway) {
        ?>
					<?= Html::submitButton(Yii::t('app', 'Pay by '. $gateway->type), ['id'=> $gateway->type .'-form-submit', 'class' =>'button', 'name' => $gateway , 'formaction' => $gateway->payNowUrl()]) ?>
<?php }?>
				</div>
				</div>	
				<?php TActiveForm::end(); ?>
			</div>
		</div>
	</div>
</section>