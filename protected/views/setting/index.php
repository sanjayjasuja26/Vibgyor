<?php
use app\models\Setting;
use yii\helpers\Inflector;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\Setting */
/* @var $dataProvider yii\data\ActiveDataProvider */

/* $this->title = Yii::t('app', 'Index'); */
$this->params['breadcrumbs'][] = [
        'label' => Yii::t('app', 'Settings'),
        'url' => [
                'index'
        ]
];
$this->params['breadcrumbs'][] = Yii::t('app', 'Index');
?>
<div class="wrapper">
	<div class="user-index">
		<div class="card ">
			<div class="setting-index">
				<?=\app\components\PageHeader::widget(['title' => Yii::t('app', 'Settings')]);?>
  			</div>
		</div>
		<div class="content-section clearfix">
			<div class="row" id="accordion" role="tablist"
				aria-multiselectable="true">
					<?php
    if (! empty($model)) {
        foreach ($model as $config) {
            $key = $config->key;
            $setConfig = \Yii::$app->settings->$key;
            ?>
            <div class="col-md-6">
					<div class="card panel-default">
						<div class="card-header" role="tab"
							id="headingOne_<?=$config->key?>">
							<strong class="card-title"> <a role="button"
								data-toggle="collapse" data-parent="#accordion"
								href="#collapseOne_<?=$config->key?>" aria-expanded="true"
								aria-controls="collapseOne"> <?=$setConfig->title?> </a>
							</strong>
						</div>
						<div id="collapseOne_<?=$config->key?>"
							class="card-collapse collapse in" role="tabpanel"
							aria-labelledby="headingOne_<?=$config->key?>">
							<div class="card-body">
								<?php
            $defaultSetting = Setting::getDefault($key);
            $defaultConfig = array_merge($defaultSetting['value'],
                    \Yii::$app->settings->$key->asArray);
            foreach ($defaultConfig as $configKey => $configDetail) {

                if ((isset($configDetail['display']) &&
                        ($configDetail['display'] == true)) ||
                        ! isset($configDetail['display'])) {
                    ?>
									<div class="row">
									<div class="col-md-6">
										<h5><?=Inflector::titleize($configKey)?></h5>
									</div>
									<div class="col-md-6">
										<p><?=Setting::checkKeyType($configDetail['type'], $configDetail['value'])?></p>
									</div>
								</div>
									<?php
                }
            }
            ?>
									<div class="text-right">
									<a href="javascript:;" class="btn btn-info showModalButton"
										data-target="<?=Url::toRoute(['/setting/ajax-update','key' => $key])?>">
										Update </a>
								</div>
							</div>
						</div>
					</div>
				</div>
					<?php
        }
    }
    ?>
					</div>
		</div>
	</div>
</div>





<?php
yii\bootstrap4\Modal::begin([
        'id' => 'modal',
        'size' => 'modal-lg'
]);
echo "<div id='modalContent'></div>";
yii\bootstrap4\Modal::end();
?>