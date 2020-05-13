<?php
use yii\widgets\ListView;
?>
<div class="tab-content">
	<div class="activity-table">
		<table class="table">
			<tbody>
<?php
echo ListView::widget([
    'dataProvider' => $dataProvider,
    // 'layout' => "{pager}{items}\n",
    'itemView' => '_list'
]);
?>
			</tbody>
		</table>
	</div>
</div>