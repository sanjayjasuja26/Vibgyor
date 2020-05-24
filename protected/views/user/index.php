<?php
/* @var $this yii\web\View */
/* @var $searchModel app\models\search\User */
/* @var $dataProvider yii\data\ActiveDataProvider */
// $this->title = Yii::t ( 'app', 'Users' );
$this->params ['breadcrumbs'] [] = [
    'label' => 'Users'
        ]
;
?>
<div class="wrapper">
    <div class="user-index">
        <div class="card ">
            <?= \app\components\PageHeader::widget(['title' => $title]); ?>
        </div>

        <div class="card panel-margin">
            <div class="card-body">
                <div class="content-section clearfix">
                    <?php echo $this->render('_grid', ['dataProvider' => $dataProvider, 'searchModel' => $searchModel]); ?>
                </div>
            </div>
        </div>
    </div>
</div>

