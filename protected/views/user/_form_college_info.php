<?php

use app\components\SActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\data\Pagination;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="card-body">
    <?php
    $form = SActiveForm::begin([
                'id' => 'user-form',
                'enableClientValidation' => true,
                'options' => [
                    'class' => 'row'
                ]
    ]);
    ?>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <?= $form->field($model, 'title')->textInput(['maxlength' => 55]) ?>
    </div>
    <div class="col-lg-6 col-md-12 col-sm-12">
        <?= $form->field($model, 'description')->textarea() ?>

    </div>

    <div class="col-md-12 bottom-admin-button btn-space-bottom">
        <div class="form-group text-right">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['id' => 'user-form-submit', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-success']) ?>
        </div>
    </div>

    <?php
    SActiveForm::end();
    ?>

</div>