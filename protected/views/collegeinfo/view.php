<?php

use app\components\useraction\UserAction;
use app\modules\comment\widgets\CommentsWidget;

/* @var $this yii\web\View */
/* @var $model app\models\Collegeinfo */

/* $this->title =  $model->label() .' : ' . $model->title; */
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Collegeinfos'), 'url' => ['index']];
$this->params['breadcrumbs'][] = (string) $model;
?>

<div class="wrapper">
    <div class=" panel ">

        <div
            class="collegeinfo-view panel-body">
                <?php echo \app\components\PageHeader::widget(['model' => $model]); ?>



        </div>
    </div>

    <div class=" panel ">
        <div class=" panel-body ">
            <?php
            echo \app\components\SDetailView::widget([
                'id' => 'collegeinfo-detail-view',
                'model' => $model,
                'options' => ['class' => 'table table-bordered'],
                'attributes' => [
                    'id',
                    [
                        'attribute' => 'user_id',
                        'format' => 'raw',
                        'value' => $model->getRelatedDataLink('user_id'),
                    ],
                    'title',
                    /* 'description:html', */
                    [
                        'attribute' => 'state_id',
                        'format' => 'raw',
                        'value' => $model->getStateBadge(),],
                    [
                        'attribute' => 'type_id',
                        'value' => $model->getType(),
                    ],
                    'created_on:datetime',
                    'updated_on:datetime',
                    [
                        'attribute' => 'created_by_id',
                        'format' => 'raw',
                        'value' => $model->getRelatedDataLink('created_by_id'),
                    ],
                ],
            ])
            ?>


            <?php echo $model->description; ?>



        </div>
    </div>
</div>
