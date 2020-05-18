<?php

use app\components\SGridView;
use app\models\User;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use app\components\MassAction;

Pjax::begin();

/**
 *
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\User $searchModel
 */
?>

<?php
if (User::isAdmin())
    echo MassAction::widget([
        'url' => Url::toRoute([
            '/user/mass'
        ]),
        'grid_id' => 'user-grid',
        'pjax_grid_id' => 'user-pjax-grid'
    ]);
?>
<div class="table table-responsive">
    <?php
    Pjax::begin([
        'id' => 'user-pjax-grid'
    ]);
    echo SGridView::widget([
        'id' => 'user-grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'name' => 'check',
                'class' => 'yii\grid\CheckboxColumn',
                'visible' => User::isAdmin()
            ],
            'id',
            'full_name',
            'email:email',
            /* 'password', */
            'contact_no',
            // 'address','city',
            /* 'country', */
            /* 'zipcode', */
            /* 'gender', */
            /* 'currency_type', */
            /* 'timezone:datetime', */
            /* 'date_of_birth:date', */
            /* 'about_me:html', */
            /*
             * ['attribute' => 'profile_file','filter'=> User::getProfileImage(),
             * 'value' => function ($data) { return $data = User::getProfileImage($data->profile_file); },],
             */
            /* 'lat', */
            /* 'long', */
            /* 'tos', */
            /* 'role_id', */
            [
                'attribute' => 'state_id',
                'filter' => $searchModel->getStateOptions(),
                'format' => 'html',
                'value' => function ($data) {
                    return $data->getStateBadge();
                }
            ],
            /* ['attribute' => 'type_id','filter'=>$searchModel->getTypeOptions(),
              'value' => function ($data) { return $data->getTypeOptions($data->type_id);  },] */
            /* 'last_visit_time:datetime', */
            /* 'last_action_time:datetime', */
            /* 'last_password_change:datetime', */
            /* 'activation_key', */
            /* 'login_error_count', */
            /* 'create_user_id', */
            [
                'attribute' => 'created_on',
                'format' => 'raw',
                'filter' => \yii\jui\DatePicker::widget([
                    'inline' => false,
                    'clientOptions' => [
                        'autoclose' => true
                    ],
                    'model' => $searchModel,
                    'attribute' => 'created_on',
                    'options' => [
                        'id' => 'created_on',
                        'class' => 'form-control'
                    ]
                ]),
                'value' => function ($data) {
                    return date('Y-m-d H:i:s', strtotime('created_on'));
                }
            ],
            [
                'class' => 'app\components\SActionColumn',
                'header' => "<a>" . Yii::t("app", 'Actions') . "</a>"
            ]
        ]
    ]);
    ?>
    <?php Pjax::end() ?>
</div>
