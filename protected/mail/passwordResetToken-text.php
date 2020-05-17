<?php
/* @var $this yii\web\View */
/* @var $user app\models\User */
$resetLink = Yii::$app->urlManager->createAbsoluteUrl([
    'site/reset-password',
    'token' => $user->password_reset_token
        ]);
?>
<?= $this->render('header.php'); ?>
<tr>
    <td align="left"
        style="font-family: Lato, sans-serif; padding-top: 30px; padding-bottom: 0; color: #333333;"><h3
            style="margin: 0; font-weight: 500; font-size: 19px;">
            Hello <?= $user->username ?>,
        </h3></td>
</tr>
<tr>
    <td align="left">
        <p> <?= \Yii::t('app', 'Follow the link below to reset your password') ?> </p>
        <?= $resetLink ?>
    </td>
</tr>
<?= $this->render('footer.php'); ?>