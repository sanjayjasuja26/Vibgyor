<?php

use app\components\SGridView;
use yii\widgets\Pjax;
/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var app\models\search\PaymentTransaction $searchModel
 */

?>
<?php Pjax::begin(['id'=>'payment-transaction-pjax-ajax-grid','enablePushState'=>false,'enableReplaceState'=>false]); ?>
    <?php echo SGridView::widget([
    	'id' => 'payment-transaction-ajax-grid-view',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions'=>['class'=>'table table-bordered'],
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn','header'=>'<a>S.No.<a/>'],

            'id',
             'name',
             'email:email',
             'amount',
            /* 'value:html',*/
             'gateway_type',
             'payment_status',
             'created_on:datetime',

            ['class' => 'app\components\SActionColumn','header'=>'<a>Actions</a>'],
        ],
    ]); ?>
<?php Pjax::end(); ?>

