<?php

/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
use app\components\TActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\LoginForm;
use app\models\User;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\LoginForm */

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => [
        'class' => ''
    ],

    'inputTemplate' => "{input}"
];

$fieldOptions2 = [
    'options' => [
        'class' => ''
    ],
    'inputTemplate' => "{input}"
];
?>
<section class="main-content hero-wrap">
	<div class="a" style="background-image:url(<?=$this->theme->getUrl('assets/images/background/login-register.jpg')?>);">
	<div class="overlay"></div>
		<div class="inner-section ">
			<div class="container">
				<div class="col-sm-12 text-center">
					<div class="logoadmin">
						<img src="<?=$this->theme->getUrl('img/dummylogo.png')?>">
					</div>
				</div>
				<div class="login-box card">
					<div class="card-block">
             <?php

            $form = TActiveForm::begin([
                'id' => 'login-form',
                'enableClientValidation' => false,
                'enableAjaxValidation' => false,
                'options' => [
                    'class' => 'form-horizontal form-material',
                    'id' => 'loginform',
                    'autocomplete' => 'off'
                ]
            ]);
            ?>
                    <h3 class="box-title m-b-20"><?=Yii::t("app", 'Sign In')?></h3>
						<div class="form-group ">
							<div class="col-xs-12">
     <?php

    echo $form->field($model, 'username', $fieldOptions1)
        ->label(false)
        ->textInput([
        'placeholder' => $model->getAttributeLabel('email')
    ])
        ->label(false)?>
                            
                    </div>
						</div>
						<div class="form-group">
							<div class="col-xs-12">
			<?php

echo $form->field($model, 'password', $fieldOptions2)
    ->label(false)
    ->passwordInput([
    'placeholder' => $model->getAttributeLabel('password')
])
    ->label(false)?>
                    </div>
						</div>
						<div class=" row">
							<div class="col-md-12">
								<div class="checkbox checkbox-primary pull-left p-t-0">
                  <?=$form->field($model, 'rememberMe')->checkbox();?>
                            </div>
								<a href="<?=Url::toRoute(['/user/recover'])?>" id="to-recover"
									class="text-dark pull-right"><i class="fa fa-lock m-r-5"></i> <?=Yii::t("app", 'Forgot password?')?></a>
							</div>
						</div>
						<div class="form-group text-center m-t-20">
							<div class="col-xs-12">
   <?=Html::submitButton('Login', ['class' => 'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light','id' => 'login','name' => 'Log In'])?>                           
                        </div>
						</div>
						<!--     <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 m-t-10 text-center">
                            <div class="social">
                                <a href="javascript:void(0)" class="btn  btn-facebook" data-toggle="tooltip" title="Login with Facebook"> <i aria-hidden="true" class="fa fa-facebook"></i> </a>
                                <a href="javascript:void(0)" class="btn btn-googleplus" data-toggle="tooltip" title="Login with Google"> <i aria-hidden="true" class="fa fa-google-plus"></i> </a>
                            </div>
                        </div>
                    </div> -->
						<div class="form-group m-b-0">
							<div class="col-sm-12 text-center">
								<p>
									Don't have an account? <a
										href="<?=Url::toRoute(['/user/signup'])?>"
										class="text-info m-l-5"><b><?=Yii::t("app", 'Sign Up')?></b></a>
								</p>
							</div>
						</div>
              	<?php
            TActiveForm::end()?>
				</div>
				</div>
			</div>
		</div>
	</div>
</section>

<script>
$(document).ready(function() {
	try {
		$('.a').ripples({
			resolution: 200,
			perturbance: 0.01
		});
	}
	catch (e) {
		$('.error').show().text(e);
	}
});
</script>
<script
	src="http://www.jqueryscript.net/demo/jQuery-Plugin-For-Water-Ripple-Animation-ripples/js/jquery.ripples.js"></script>