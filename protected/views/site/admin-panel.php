<?php

/**
 *@copyright : ToXSL Technologies Pvt. Ltd. < www.toxsl.com >
 *@author	 : Shiv Charan Panjeta < shiv@toxsl.com >
 */
use app\components\TGridView;
use app\models\User;
use yii\data\ActiveDataProvider;
/* @var $this yii\web\View */
//$this->title = Yii::t ( 'app', 'Dashboard' );
$this->params ['breadcrumbs'] [] = [
		'label' => Yii::t ( 'app', 'Dashboard' ),
		
];
?>
<div class="wrapper">

<div class="page-head card-body">
	
		<h1>Hello</h1>
		<strong>
                    <?php echo Yii::$app->user->identity->email;?></strong>
                    <br>
	

	<span class="sub-title">Welcome to your <b><?php echo User::getRoleOptions(Yii::$app->user->identity->role_id);?></b>
		dashboard
	</span>

</div>
<div class="wrapper">
	<!--state overview start-->
	<div class="row state-overview">
		<div class="col-lg-3 col-sm-6">
			<section class="card purple">
				<div class="symbol">
					<i class="fa fa-user"></i>
				</div>
				<div class="value white">
					<h1 class="timer" data-from="0" data-to="320" data-speed="1000">
						<?php echo User::find()->count();?>
					</h1>
					<p>Total Users</p>
				</div>
			</section>
		</div>
		<div class="clearfix"></div>
		<div class="card">
		<div class="card-body">
		
		
			
		<?php 
		$query = User::find();
     $dataProvider = new ActiveDataProvider ( [ 
				'query' => $query 
		] );
     $dataProvider->pagination->pagesize =5;
		echo TGridView::widget ( [ 
				'dataProvider' => $dataProvider,	
				'summary'=>'',
				
				'columns' => [ 
						[ 
								'class' => 'yii\grid\SerialColumn' 
						],
						'full_name',
						'email:email',
						'contact_no',
						
// 						[ 
// 								'class' => 'yii\grid\ActionColumn' 
// 						] 
				] 
		] );
		?>
	
			
	</div>
	</div>
	</div>
	</div>
	</div>
	<!--state overview end-->
