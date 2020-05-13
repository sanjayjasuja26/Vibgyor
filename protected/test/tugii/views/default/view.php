<?php
/**
*@copyright   : ToXSL Technologies Pvt. Ltd < https://toxsl.com >
*@author      : Shiv Charan Panjeta  < shiv@toxsl.com >
*/
 
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\gii\components\ActiveField;
use yii\gii\CodeFile;

/* @var $this yii\web\View */
/* @var $generator yii\gii\Generator */
/* @var $id string panel ID */
/* @var $form yii\widgets\ActiveForm */
/* @var $results string */
/* @var $hasError boolean */
/* @var $files CodeFile[] */
/* @var $answers array */

$this->title = $generator->getName ();
$templates = [ ];
foreach ( $generator->templates as $name => $path ) {
	$templates [$name] = "$name ($path)";
}
?>
<div class="default-view">
	<h1><?= Html::encode($this->title) ?></h1>

	<p><?= $generator->getDescription() ?></p>

    <?php
				
$form = ActiveForm::begin ( [ 
						'id' => "$id-generator",
						'successCssClass' => '',
						'enableClientValidation' => false,
						'fieldConfig' => [ 
								'class' => ActiveField::className () 
						] 
				] );
				?>
        <div class="row">
		<div class="col-lg-8 col-md-10">
                <?=$this->renderFile ( $generator->formView (), [ 'generator' => $generator,'form' => $form ] )?>
                <?=$form->field ( $generator, 'template' )->sticky ()->label ( 'Code Template' )->dropDownList ( $templates )->hint ( '
                        Please select which set of the templates should be used to generated the code.
                ' )?>
                <div class="form-group">
                    <?= Html::submitButton('Preview', ['name' => 'preview', 'class' => 'btn btn-primary'])?>

                    <?php if (isset($files)): ?>
                        <?= Html::submitButton('Generate', ['name' => 'generate', 'class' => 'btn btn-success'])?>
                    <?php endif; ?>
                </div>
		</div>
	</div>

        <?php
								if (isset ( $results )) {
									echo $this->render ( 'view/results', [ 
											'generator' => $generator,
											'results' => $results,
											'hasError' => $hasError 
									] );
								} elseif (isset ( $files )) {
									echo $this->render ( 'view/files', [ 
											'id' => $id,
											'generator' => $generator,
											'files' => $files,
											'answers' => $answers 
									] );
								}
								?>
    <?php ActiveForm::end(); ?>
</div>
