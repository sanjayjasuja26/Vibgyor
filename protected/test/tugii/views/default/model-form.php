<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
            'id' => 'model-form',
            // 'action' => Yii::$app->urlManager->createUrl ( '/crud/default/process' ),
            'attributes' => [
                'db_connection' => 'DB Connection'
            ]
        ]);
?>

<div class="tugii-title">Yii2 Auto CRUD</div>


<div>
    <div class="tugii-info">
        Use whichever database connection to be queried. Default is "db". <br>
        This refers to "Yii::$app->db"
    </div>
<?= $form->field($model, 'db_connection') ?>
</div>
<div>
    <div class="tugii-info">Namespace path to the models directory. Default
        is automatically added.</div>
<?= $form->field($model, 'models_path') ?>
</div>
<div>
    <div class="tugii-info">Namespace path to the model search directory.
        Default is automatically added. This can be the same as the models
        path.</div>
<?= $form->field($model, 'models_search_path') ?>
</div>

<div>
    <div class="tugii-info">Overwrite existing models</div>
<?= $form->field($model, 'override_models')->checkbox() ?>
</div>
<div>
    <div class="tugii-info">Comma delimited list of models to skip. Note,
        do NOT add .php</div>
<?= $form->field($model, 'exclude_models') ?>
</div>


<div class="form-group">
    <div class="">
<?= Html::submitButton('Run', ['class' => 'btn btn-primary', 'name' => 'button-submit']) ?>
    </div>
</div>

<?php ActiveForm::end() ?>





