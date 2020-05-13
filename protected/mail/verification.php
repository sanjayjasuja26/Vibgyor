<?php

/* @var $this yii\web\View */
/* @var $user app\models\User */
$Link = $user->getVerified();

?>
<?=$this->render('header.php');?>
<tr>
	<td colspan="2" style="padding: 30px 0;">
		<p
			style="color: #1d2227; line-height: 28px; font-size: 25px; margin: 12px 10px 16px 10px; font-weight: 400;">Welcome
			to <?=\Yii::$app->name?></p>
		<p style="margin: 0 10px 10px 10px; padding: 0; font-size: 14px;">
			Thanks for signing up. To send your first <?=\Yii::$app->params['company']?>, please verify<br>
			your email address by clicking the button below.
		</p>
		<p>
			<a
				style="display: inline-block; text-decoration: none; padding: 11px 20px; background-color: #6dbd63; border: 1px solid #6dbd63; border-radius: 3px; color: #FFF; font-weight: bold;"
				href="<?php

echo $Link?>" target="_blank">Activate your account and
				log in</a>
		</p>
		<p>
			<font size="2" color="#333"> If above link isn't working, please copy
				and paste it directly in you browser's URL field to get started.<br />
				<br />
	                          
	                              <?php

echo $Link;
                            ?>
								</font>
		</p>
	</td>
</tr>
<?=$this->render('footer.php');?>

