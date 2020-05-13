<?php
use app\modules\payment\models\Gateway;
use app\modules\payment\models\GatewaySetting;
use yii\helpers\Url;
?>
<div class="panel-body">

<?php
$key = Gateway::gateway ( GatewaySetting::GATEWAY_TYPE_STRIPE );
$amount = 0;
$quantity = 1;
$symbol = "USD";
?>

<form action="<?php echo Url::toRoute(['payment/stripe/payow']) ?>"
		method="POST" id="event-form1">
		<input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>"
			value="<?= Yii::$app->request->csrfToken; ?>" /> <input type="hidden"
			value="<?php echo $amount?>" name="amount"> <input type="hidden"
			value="<?php echo $quantity?>" name="quantity"><input type="hidden"
			value="<?php echo $symbol?>" name="currency">

		<script src="https://checkout.stripe.com/checkout.js"
			class="stripe-button"
			data-key="<?php echo $key->stripe_publishable_key?>"
			data-amount="<?php echo $amount?>"
			data-email="<?php echo \Yii::$app->params['adminEmail'];?>"
			data-name="<?php echo \Yii::$app->name?>" data-description=""
			data-image="<?php echo $this->theme->getUrl('img/stripe_logo.png')?>"
			data-locale="auto">
  		</script>
	</form>
</div>
