<?php
/* @var $this yii\web\View */
/* @var $model app\models\User */

/*
 * $this->title = Yii::t ( 'app', 'Update {modelClass}: ', [
 * 'modelClass' => 'User'
 * ] ) . ' ' . $model->id;
 */
$this->params ['breadcrumbs'] [] = [
    'label' => Yii::t('app', 'College ')
];
$this->params ['breadcrumbs'] [] = [
    'label' => 'update college info',
        // 'url' => $model->getUrl()
];
//$this->params ['breadcrumbs'] [] = Yii::t('app', 'Update');
$this->params ['breadcrumbs'] = '';
?>
<div class="wrapper">
    <div class="card ">
        <?= \app\components\PageHeader::widget(['showAdd' => false, 'title' => 'College info']); ?>
    </div>

    <div class="content-section clearfix panel">
        <?=
        $this->render('_form_college_info', ['model' => $model,
            'user_model' => $user_model])
        ?></div>
</div>

