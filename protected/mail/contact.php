<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

// $Link = $user->getLoginUrl();
?>
<?=$this->render('header.php');?>
<tr>
	<td align="left"
		style="font-family: Lato, sans-serif; padding-top: 30px; padding-bottom: 0; color: #333333;"><h3
			style="margin: 0; font-weight: 500; font-size: 19px;">Hi <?php

echo Html::encode($user->name)?>,</h3></td>
</tr>

<tr>

	<td align="left">
		<p
			style="font-size: 14px; padding: 0 0px 23px; border-bottom: 1px solid #ececec; text-align: left; margin-bottom: 8px;">
			A User <b><?=$user->name?> </b> has sent a new request.<br> User's
			request details is given below; <br> <b>Name:</b><?=Html::encode($user->name);?>
			<br> <b>Email:</b> <?=Html::encode($user->email);?>
			<br> <b>Message:</b> <?=Html::encode($user->body);?>			
			<br>
		</p>
	</td>
</tr>

<?=$this->render('footer.php');?>
  
  