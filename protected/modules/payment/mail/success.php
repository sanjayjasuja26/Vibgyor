<?= $this->render('@app/mail/header.php'); ?>
<tr>
	<td align="left"
		style="font-family: Lato, sans-serif; padding-top: 30px; padding-bottom: 0; color: #333333;"><h3
			style="margin: 0; font-weight: 500; font-size: 19px;">Dear Admin</h3></td>
</tr>
<tr>
	<td align="left">
		<p
			style="font-size: 14px; padding: 0 0px 23px; border-bottom: 1px solid #ececec; text-align: left; color: #666; margin-bottom: 8px;">
				<?= \Yii::t('app', "Payment transaction has been made.") ?> </br>
				<?= \Yii::t('app', "Please find the transaction details below.") ?>
		</p>
<?php
if (! empty($model)) {
    ?>
		<p>
			Transaction ID :  <?= $model->transaction_id ?>
		</p>
		<p>
			Amount :  <?= $model->amount ?>
		</p>
		<p>
			Currency :  <?= $model->currency ?>
		</p>
		<p>
			Payment Status :  <?= $model->getState() ?>
		</p>
		<p>
			Description :  <?= $model->description ?>
		</p>
				<?php
}
?>

	</td>
</tr>
<?= $this->render('@app/mail/footer.php'); ?>