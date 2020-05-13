<li class="list-group-item">
	<div class="row">
		<div class="col-xs-3 col-md-2">
			<img
				src="https://p.w3layouts.com/demos/minimal_admin_panel/web/images/in.jpg"
				class="img-circle img-responsive" alt="" />
		</div>
		<div class="col-xs-9 col-md-9">
			<div class="mic-info"><?= $model->content;?></div> 
			<div class="mic-info"><?= ($model->createdBy != null) ?  "By " . $model->createdBy->linkify() : ''?> 
			<?= Yii::$app->formatter->asRelativeTime($model->created_on, 'now');?></div>
		</div>
	</div>
</li>