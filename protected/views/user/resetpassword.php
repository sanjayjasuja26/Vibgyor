<?php

/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
use app\components\TActiveForm;
use yii\helpers\Html;
use yii\helpers\Inflector;
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\PasswordResetRequestForm */

$this->params['breadcrumbs'][] = [
    'label' => 'Users',
    'url' => [
        'user/index'
    ]
];

$this->params['breadcrumbs'][] = Inflector::humanize(Yii::$app->controller->action->id);

?>
<section class="main-content hero-wrap">
	<div class="a" style="background-image:url(<?=$this->theme->getUrl('assets/images/background/login-register.jpg')?>);">
		<div class="overlay"></div>
		<div class="inner-section ">
			<div class="login-box card">
				<div class="card-block">
        <?php

        $form = TActiveForm::begin([
            'id' => 'changepassword-form',
            'enableAjaxValidation' => false,
            'enableClientValidation' => false
        ]);
        ?>
            <div class="card panel-default">

						<div class="card-body">
							<div class="text-center">
								<h3>Reset password</h3>
								<p> <?=\Yii::t("app", "Please fill out your email. A link to reset password will be sent there.")?></p>
							</div>
						
           
               <?=$form->field($model, 'password', ['inputOptions' => ['placeholder' => '']])->passwordInput()?>
               <?=$form->field($model, 'confirm_password', ['inputOptions' => ['placeholder' => '']])->passwordInput()?>
                
                <?=Html::submitButton('Change password', ['class' => 'btn btn-primary'])?>
                
           <?php

        TActiveForm::end();
        ?>
        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
