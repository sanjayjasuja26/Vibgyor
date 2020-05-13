<?php
use app\components\TActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \app\models\SignupForm */

$this->title = 'Signup';
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
    'id' => 'form-add-admin',
    'options' => [
        'class' => 'form-horizontal'
    ]
]);
?>


   
      
     
	   <div class="text-center">
		<h3>Admin Registration Form</h3>
	   </div>
			


					
						
						<?=$form->field($model, 'full_name', ['template' => '<div class="col-sm-12">{input}{error}</div>'])->textInput(['maxlength' => true,'placeholder' => 'Full Name'])->label(false)?>



						<?=$form->field($model, 'email', ['template' => '<div class="col-sm-12">{input}{error}</div>'])->textInput(['maxlength' => true,'placeholder' => 'Email'])->label(false)?>


							<?=$form->field($model, 'password', ['template' => '<div class="col-sm-12">{input}{error}</div>'])->passwordInput(['maxlength' => true,'placeholder' => 'Password'])->label(false)?>


							<?=$form->field($model, 'confirm_password', ['template' => '<div class="col-sm-12">{input}{error}</div>'])->passwordInput(['maxlength' => true,'placeholder' => 'Confirm Password'])->label(false)?>
						
                    <?=Html::submitButton('Signup', ['class' => 'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light','name' => 'signup-button'])?>
               



								<!-- driver form ends -->



					

				</div>
			
			
			<div class="card-footer text-center">
			    <div class="registration m-t-20 m-b-20">
							Already have an account ?<a href="<?php

    echo Url::toRoute([
        'user/login'
    ]);
    ?>">
								Login </a>
						</div>
			   </div>
			   
			   	<?php

    TActiveForm::end();
    ?>
		
</div>
</div>
</div>
</div>

</section>