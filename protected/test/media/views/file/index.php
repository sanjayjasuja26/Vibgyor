<?php
use app\modules\media\widgets\Gallery;

/* @var $this yii\web\View */
/* @var $searchModel app\modules\media\models\search\File */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* $this->title = Yii::t('app', 'Index'); */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Files'),
    'url' => [
        'index'
    ]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Index');
?>
<div class="wrapper">
	<div class="user-index">
		<div class="panel">
			<div class="file-index"></div>
		</div>
		<div class="panel panel-margin">
			<div class="panel-body">
				<div class="content-section clearfix">
					<?php
    echo Gallery::widget();
    ?>
				</div>
			</div>
		</div>
	</div>
</div>

