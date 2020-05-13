
	<div class="card widget notice-view">
		<div class="Card-header">
			<h3 class="card-title">
				<span> </span> Notices
			</h3>
		</div> 	<?php //Pjax::begin(['id'=>'notices']); ?>
	<div id='notices' class="card-body list">

			<div class="content-list content-image menu-action-right">
				<ul class="list-wrapper notice-list">

<?php
echo \yii\widgets\ListView::widget([
    'dataProvider' => $notices,
    
    'summary' => false,
    
    'itemOptions' => [
        'class' => 'item'
    ],
    'itemView' => '_view',
    'options' => [
        'class' => 'list-view notice-list'
    ]
]);
?>
</ul>

			</div>
		</div><?php //Pjax::end(); ?>

</div>

