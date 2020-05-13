<?php
use app\modules\payment\assets\PaymentAsset;
use app\modules\payment\models\Transaction;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
PaymentAsset::register($this);
$this->title = "Payment Success";
?>
<div class="cx-section contact-section gray-bg payment-module">
	<div class="content">
		<div class="wrapper-1">
			<div class="wrapper-2">
				<h3 class="thanks-text"> <?php
    if (! empty($model)) {
        if ($model->payment_status == Transaction::PAYMENT_STATUS_SUCCESS) {
            echo \Yii::t('app', "Thank You !!");
        } elseif ($model->payment_status == Transaction::PAYMENT_STATUS_FAIL) {
            echo \Yii::t('app', "Failure !!");
        } elseif ($model->payment_status == Transaction::PAYMENT_STATUS_PENDING) {
            echo \Yii::t('app', "Pending !!");
        } elseif ($model->payment_status == Transaction::PAYMENT_STATUS_CANCEL) {
            echo \Yii::t('app', "Cancel !!");
        }
    }
    ?> </h3>
			
			<?php
if (! empty($model)) {
    if ($model->payment_status == Transaction::PAYMENT_STATUS_SUCCESS) {
        ?>
					<p><?= \Yii::t('app',"Thank you for your payment. Your transaction has been completed.") ?></p>
						<?php
    } elseif ($model->payment_status == Transaction::PAYMENT_STATUS_FAIL) {
        ?>
							<p><?php
        $model->getPaymentResponse();
        ?></p>
						<?php } elseif ($model->payment_status == Transaction::PAYMENT_STATUS_PENDING) { ?>
						<?php } elseif ($model->payment_status == Transaction::PAYMENT_STATUS_CANCEL) { ?>
					<?php
    }
}
if (! empty($model)) {
    if ($model->payment_status == Transaction::PAYMENT_STATUS_SUCCESS) {
        ?>
					<div>
					<strong> <?= $model->transaction_id ?> </strong>
					<p> <?= \Yii::t('app', 'This is your transaction id please keep it.')  ?>
					<?= \Yii::t('app', 'Our representative will contact you soon.') ?> </p>
				</div>
				<?php
    }
}
?>
			<a href="<?= Url::home() ?>" class="button">go home</a>
			</div>
		</div>
	</div>
</div>



<link
	href="https://fonts.googleapis.com/css?family=Kaushan+Script|Source+Sans+Pro"
	rel="stylesheet">