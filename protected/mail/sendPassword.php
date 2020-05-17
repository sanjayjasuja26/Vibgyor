<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user app\models\User */

$Link = $user->getLoginUrl();
?>
<?= $this->render('header.php'); ?>
<tr>
    <td align="left">
        <p> <?= \Yii::t('app', 'Hello') ?>
        <p>
            <?= \Yii::t('app', 'Thank you for registering with') ?>
            <?php echo Yii::$app->name ?>.
            <?= \Yii::t('app', 'Your login Credentials are') ?>

            <br>
            <?= \Yii::t('app', 'Email') ?> <?php
            echo $user->email;
            ?>
            <br>
            <?php
            echo \Yii::t('app', 'Password') . " " . $user->password;
            ?>
            <br>

        <p><?= \Yii::t('app', 'Follow the link') ?></p>

        <p><?= Html::a(Html::encode($Link), $Link) ?></p>
    </td>
</tr>
<?= $this->render('footer.php'); ?>