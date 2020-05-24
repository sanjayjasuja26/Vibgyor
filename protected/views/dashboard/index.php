<?php

use app\components\notice\Notices;
use app\controllers\DashboardController;
use app\models\EmailQueue;
use app\models\User;
use app\modules\page\models\Page;
use miloschuman\highcharts\Highcharts;
use yii\helpers\Url;
use app\modules\logger\models\Log;

/* @var $this \yii\web\View */
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Dashboard')
];
?>
<?= Notices::widget(); ?>
<?php
$isConfig = \Yii::$app->settings->isConfig;
if (!$isConfig) {
    ?>
    <div>
        <div class="alert alert-info">
            <strong> Info !! </strong> Your app is not configure properly <b><a
                    href="<?= Url::toRoute(['/setting/index']) ?>"> Click Here </a></b> To
            configure..
        </div>
    </div>
    <?php
}
?>

<div class="row">
    <?php if (User::isAdmin()) { ?>
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <a href='<?= Url::toRoute(['user/index']); ?>'>
                <div class="card card-inverse card-info">
                    <div class="box  text-center">
                        <h1 class="font-light text-white"><?= User::find()->count(); ?></h1>
                        <h6 class="text-white"><?= Yii::t("app", 'Total Users') ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <a href='<?= Url::toRoute(['//page']); ?>'>
                <div class="card card-primary card-inverse">
                    <div class="box text-center">
                        <h1 class="font-light text-white">23</h1>
                        <h6 class="text-white"><?= Yii::t("app", 'Total Pages') ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <a href='<?= Url::toRoute(['email-queue/index']); ?>'>
                <div class="card card-inverse card-success">
                    <div class="box text-center">
                        <h1 class="font-light text-white"><?= EmailQueue::find()->count(); ?></h1>
                        <h6 class="text-white"><?= Yii::t("app", 'Total Emails') ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <a href='<?= Url::toRoute(['user/index']); ?>'>
                <div class="card card-inverse card-warning">
                    <div class="box text-center">
                        <h1 class="font-light text-white">12</h1>
                        <h6 class="text-white"><?= Yii::t("app", 'Total Logs') ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <?php
    }
    if (User::isCollege()) {
        ?>
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <a href='<?= Url::toRoute(['collegecounseller/index']); ?>'>
                <div class="card card-inverse card-info">
                    <div class="box  text-center">
                        <h1 class="font-light text-white"><?= app\models\Collegecounseller::find()->where(['college_id' => Yii::$app->user->id])->count(); ?></h1>
                        <h6 class="text-white"><?= Yii::t("app", 'Total Counsellor') ?></h6>
                    </div>
                </div>
            </a>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-md-6 col-lg-3">
            <a href='#'>
                <div class="card card-primary card-inverse">
                    <div class="box text-center">
                        <h1 class="font-light text-white">23</h1>
                        <h6 class="text-white"><?= Yii::t("app", 'Total Events') ?></h6>
                    </div>
                </div>
            </a>
        </div>
    <?php }
    ?>
</div>
<?php
if (User::isAdmin()) {
    ?>
    <div class="row">
        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-block">
                    <?php
                    $data = DashboardController::MonthlySignups();

                    echo Highcharts::widget([
                        'options' => [
                            'credits' => array(
                                'enabled' => false
                            ),
                            'title' => [
                                'text' => 'Monthly Users Registered'
                            ],
                            'chart' => [
                                'type' => 'column'
                            ],
                            'xAxis' => [
                                'categories' => array_keys($data)
                            ],
                            'yAxis' => [
                                'title' => [
                                    'text' => 'Count'
                                ]
                            ],
                            'series' => [
                                [
                                    'name' => 'Users',
                                    'data' => array_values($data)
                                ]
                            ]
                        ]
                    ]);
                    ?>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12">
            <div class="card">
                <div class="card-block">
                    <?php
                    $data = DashboardController::MonthlySignups();
                    ?>
                    <?php
                    echo Highcharts::widget([
                        'scripts' => [
                            'highcharts-3d',
                            'modules/exporting'
                        ],
                        'options' => [
                            'credits' => array(
                                'enabled' => false
                            ),
                            'chart' => [
                                'plotBackgroundColor' => null,
                                'plotBorderWidth' => null,
                                'plotShadow' => false,
                                'type' => 'pie'
                            ],
                            'title' => [
                                'text' => 'Statistics'
                            ],
                            'tooltip' => [
                                'valueSuffix' => ''
                            ],
                            'plotOptions' => [
                                'pie' => [
                                    'allowPointSelect' => true,
                                    'cursor' => 'pointer',
                                    'dataLabels' => [
                                        'enabled' => true
                                    ],
                                    // 'format' => '<b>{point.name}</b>: {point.percentage:.1f} %'
                                    'showInLegend' => true
                                ]
                            ],
                            'htmlOptions' => [
                                'style' => 'min-width: 100%; height: 400px; margin: 0 auto'
                            ],
                            'series' => [
                                [
                                    'name' => 'Total Count',
                                    'colorByPoint' => true,
                                    'data' => [
                                        [
                                            'name' => 'Inactive User',
                                            'color' => '#0096FF',
                                            'y' => (int) User::findActive(0)->count(),
                                            'sliced' => true,
                                            'selected' => true
                                        ],
                                        [
                                            'name' => 'Active User',
                                            'color' => '#1FAE66',
                                            'y' => (int) User::findActive()->count(),
                                            'sliced' => true,
                                            'selected' => true
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]);
                    ?>
                </div>

            </div>
        </div>
    </div>
    <?php
}
if (User::isAdmin()) {
    ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <header class="card-title card-header"><?= Yii::t("app", 'Users List') ?></header>
                <div class="card-body">
                    <?php
                    $searchModel = new \app\models\search\User();
                    $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                    $dataProvider->pagination->pageSize = 5;
                    ?>
                    <?php
                    echo $this->render('//user/_grid', [
                        'dataProvider' => $dataProvider,
                        'searchModel' => $searchModel
                    ]);
                    ?>		
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<!-- Row -->

<!-- .right-sidebar -->
<div class="right-sidebar">
    <div class="slimscrollright">
        <div class="rpanel-title">
            Service Panel <span><i class="ti-close right-side-toggle"></i></span>
        </div>
        <div class="r-panel-body">
            <ul id="themecolors" class="m-t-20">
                <li><b>With Light sidebar</b></li>
                <li><a href="javascript:void(0)" data-theme="default"
                       class="default-theme">1</a></li>
                <li><a href="javascript:void(0)" data-theme="green"
                       class="green-theme">2</a></li>
                <li><a href="javascript:void(0)" data-theme="red" class="red-theme">3</a></li>
                <li><a href="javascript:void(0)" data-theme="blue"
                       class="blue-theme working">4</a></li>
                <li><a href="javascript:void(0)" data-theme="purple"
                       class="purple-theme">5</a></li>
                <li><a href="javascript:void(0)" data-theme="megna"
                       class="megna-theme">6</a></li>
                <li class="d-block m-t-30"><b>With Dark sidebar</b></li>
                <li><a href="javascript:void(0)" data-theme="default-dark"
                       class="default-dark-theme">7</a></li>
                <li><a href="javascript:void(0)" data-theme="green-dark"
                       class="green-dark-theme">8</a></li>
                <li><a href="javascript:void(0)" data-theme="red-dark"
                       class="red-dark-theme">9</a></li>
                <li><a href="javascript:void(0)" data-theme="blue-dark"
                       class="blue-dark-theme">10</a></li>
                <li><a href="javascript:void(0)" data-theme="purple-dark"
                       class="purple-dark-theme">11</a></li>
                <li><a href="javascript:void(0)" data-theme="megna-dark"
                       class="megna-dark-theme ">12</a></li>
            </ul>
        </div>
    </div>
</div>