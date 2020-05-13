<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/

/* @var $this yii\web\View */
/* @var $user app\models\User */
?>
<?=$this->render('header.php');?>
<tr>
	<td align="left">
		<div class="password-reset">
			<h3>Hi Admin</h3>
			<p>There was an error/crash reported in <?=\Yii::$app->name;?>!</p>
			<br>
			<p>Details are as follows:</p>
			<br>
			<p>Time: <?=$user->create_time;?></p>
			<p>Error Code: HTTP-<?=$user->error;?></p>
			<p>Url: <?=isset($user->api) ? $user->api : 'Not Reported';?></p>
			<br>
			<p><?=\Yii::$app->name;?> Team</p>
			<p style="margin: 0 auto;">&copy; <?=date("Y")?> <?=\Yii::$app->name;?> | All Rights Reserved</p>
		</div>
	</td>
</tr>
<?=$this->render('footer.php');?>