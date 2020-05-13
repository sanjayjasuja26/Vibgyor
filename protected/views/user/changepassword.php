<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->params['breadcrumbs'][] = [
    'label' => 'Users',
    'url' => [
        'user/index'
    ]
];

$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Change Password')
];

?>

<section class="main-content hero-wrap">
	<div class="a" style="background-image:url(<?=$this->theme->getUrl('assets/images/background/login-register.jpg')?>);">
		<div class="overlay"></div>
		<div class="inner-section ">
			<div class="login-box card">
				<div class="card-block">
					<h3><?= Html::encode($this->title)?>
	</h3>
					<header class="card-header"> Please fill out the following fields
						to change password </header>

					<div class="card-body">
						<div class="col-md-6 offset-md-3 panel-body">
    		<?php

    $form = ActiveForm::begin([
        'id' => 'changepassword-form',
        'enableAjaxValidation' => true,
        'options' => [
            'class' => 'form-horizontal'
            // 'layout' => 'horizontal'
        ],
        'fieldConfig' => [
            'template' => "{label}\n<div class=\"col-lg-12\">
                        {input}</div>\n<div class=\"col-md-6 col-md-offset-4\">
                        {error}</div>",
            'labelOptions' => [
                'class' => 'col-lg-12 control-label'
            ]
        ]
    ]);
    // 'action'=>['api/user/change-password'],

    ?>

     			<?php //$form->field ( $model, 'oldPassword', [ 'inputOptions' => [ 'placeholder' => '','value' => '' ] ] )->label ()->passwordInput ()?>
            	<?=$form->field ( $model, 'newPassword', [ 'inputOptions' => [ 'placeholder' => '','value' => '' ] ] )->label ()->passwordInput ()?>
        		<?=$form->field ( $model, 'confirm_password', [ 'inputOptions' => [ 'placeholder' => '' ] ] )->label ()->passwordInput ()?>
        	<div class="form-group">
								<div class=" text-center">
					      <?=Html::submitButton ( 'Change password', [ 'class' => 'btn btn-success','name' => 'changepassword-button' ] )?>
            	</div>

    <?php ActiveForm::end(); ?>
    <div class="gap-20"></div>

							</div>

						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>