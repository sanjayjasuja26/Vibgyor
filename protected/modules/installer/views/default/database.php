<?php
use yii\helpers\Url;
?>

<div id="database-form" class="panel panel-default">
	<div class="alert alert-wrapper">
<?php if(Yii::$app->session->hasFlash('error')) {?>
<div class="alert alert-danger"><?php echo Yii::$app->session->getFlash('error');?></div>
<?php }?>
</div>
	<div class="panel-heading">
		<h2 class="text-center">Database Configuration!</h2>
	</div>
	<div class="panel-body">
		<p>Below you have to enter your database connection details. If
			you’re not sure about these, please contact your administrator or
			web host.</p>

		<?php
		$form = \yii\widgets\ActiveForm::begin ( [ 
				'id' => 'database-form',
				'enableAjaxValidation' => FALSE 
		] );
		?>

		<hr />

		<div class="form-group">
			<?=$form->field ( $model, 'host' )->textInput ( [ 'autofocus' => 'on','autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'You should be able to get this info from your web host, if localhost does not work.' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $model, 'port' )->textInput ( [ 'autofocus' => 'on','autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'You should be able to get this info from your web host, if localhost does not work.' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $model, 'username' )->textInput ( [ 'autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'Your MySQL username' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $model, 'password' )->passwordInput ( [ 'class' => 'form-control' ] )->hint ( 'Your MySQL password' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $model, 'db_name' )->textInput ( [ 'autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'The name of the database you want to run your application in.' )?>
		</div>


		<div class="form-group">
			<?=$form->field ( $model, 'table_prefix' );?>
		</div>
		<hr />
		
		<div class="form-group">
			<?=$form->field ( $mail, 'is_mail_prod' )->checkbox(['id' => 'mail-button' ]);?>
		</div>
		<hr />
		
		<div id="form-mail-prod" style="display:none">
			<hr />

		<div class="form-group">
			<?=$form->field ( $mail, 'host' )->textInput ( [ 'autofocus' => 'on','autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'You host.' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $mail, 'port' )->textInput ( [ 'autofocus' => 'on','autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'You port.' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $mail, 'username' )->textInput ( [ 'autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'Your smtp username' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $mail, 'password' )->passwordInput ( [ 'class' => 'form-control' ] )->hint ( 'Your smtp password' )?>
		</div>

		<hr />

		<div class="form-group">
			<?=$form->field ( $mail, 'encryption' )->textInput ( [ 'autocomplete' => 'off','class' => 'form-control' ] )->hint ( 'Encryption.' )?>
		</div>
		
		</div>
		
		

		<?= \yii\helpers\Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>

		<?php \yii\widgets\ActiveForm::end(); ?>
	</div>
</div>


<script>
$(document).on("load",function(e){
	$("#form-mail-prod").hide();
});
$(document).on("change","#mail-button",function(e){
	$("#form-mail-prod").toggle();
})
</script>