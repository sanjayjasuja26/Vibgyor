

<div class="tugii-default-index">

	<div class="page-header">
		<h1>
			Welcome to TuGii <small>a magical tool that will write code for you</small>
		</h1>
	</div>

	<p class="lead">Start the fun with the following options:</p>

	<div class="row">
    <?php
				use yii\helpers\Html;
				
				$menu = [ 
						[ 
								'label' => Yii::t ( 'app', 'Home' ),
								'url' => [ 
										'index' 
								] 
						],
						[ 
								'label' => Yii::t ( 'app', 'Create all Models' ),
								'url' => [ 
										'models' 
								] 
						],
						[ 
								'label' => Yii::t ( 'app', 'Create all CRUDs' ),
								'url' => [ 
										'cruds' 
								] 
						],
						[ 
								'label' => Yii::t ( 'app', 'Create all Apis' ),
								'url' => [ 
										'apis' 
								] 
						] 
				];
				?>
        <?php foreach ($menu as $id => $generator): ?>
        <div class="generator col-lg-4">
			<h3><?= Html::encode($generator['label']) ?></h3>
			<p><?= $generator['label'] ?></p>
			<p><?= Html::a('Lets Try »', $generator['url'], ['class' => 'btn btn-default']) ?></p>
		</div>
        <?php endforeach; ?>
    </div>


</div>
