<?php
/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\ContactForm */
use app\components\TActiveForm;

/*
 * $this->title = 'Contact';
 * $this->params ['breadcrumbs'] [] = $this->title;
 */
?>
<div class="page--header pt--120 pb--120 hero-wrap text-center topbanner-back bg--img" style="background-image:url(<?=$this->theme->getUrl('assets/images/background/login-register.jpg')?>);">
	<div class="container">
		<div class="title">
			<h2 class="h1 text-white">Contact Us</h2>
		</div>

		<ul class="breadcrumb text-gray ff--primary">
			<li><a href="index.php" class="btn-link">Home</a></li>
			<li class="active"><span class="text-primary">Contact Us</span></li>
		</ul>
	</div>
</div>

<div class="site-about pt--60 pb--60">
	<div class="container">
		<div class="row">
			<div class="col-sm-12 col-md-12">
				<div class="form-outer padd-lt">
			 <?php
    $form = TActiveForm::begin([
        'id' => 'contact-form',
        'options' => [
            'class' => 'driver-form form-horizontal'
        ],
        'fieldConfig' => [
            'template' => "{input}{error}"
        ]
    ]);
    ?>
            	<div class="row">
					<div class="col-md-6">	
                     <?php echo $form->field ( $model, 'name')->textInput ( [ 'placeholder' => 'Name' ] )->label ( false )?>
                     </div>
						<div class="col-md-6">	
                     <?php echo  $form->field($model, 'email')->textInput(['placeholder'=>'Email'])->label(false)?>
                     </div>
					</div>
                 	
                    <?php echo  $form->field($model, 'subject')->textInput(['placeholder'=>'Subject'])->label(false)?>
                     <?php echo $form->field ( $model, 'body' )->textArea ( [ 'rows' => 6,'placeholder' => 'Message' ] )->label ( false )?>
					<?php

    echo \yii\helpers\Html::submitButton('Submit', [
        'class' => 'btn btn-info btn-lg btn-block text-uppercase waves-effect waves-light',
        'name' => 'submit-button'
    ])?>
	
                    <?php TActiveForm::end(); ?>                
				</div>
			</div>
		</div>
	</div>
</div>