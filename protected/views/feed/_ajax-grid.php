<?php
use app\components\useraction\UserAction;
use app\models\Feed;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;
?>
<div class="card ">
	<div class="card-body ">
		<div class="col-md-6">

			<div class="activity-table">
				<table class="table">
					<tbody>
										<?php Pjax::begin(['id'=>'feed-list-view-project','enablePushState'=>false,'enableReplaceState'=>false]); ?>
                                        <?php
                                        echo ListView::widget([
                                            'dataProvider' => $dataProvider,
                                            'layout' => "{summary}\n\n{items}",
                                            'itemView' => '//feed/_list'
                                        ]);
                                        ?>
                                        <?php Pjax::end(); ?>		
								</tbody>
				</table>

			</div>
			<span><?=Html::a('Show More ... ', Url::toRoute('/feed/more'), ['class' => 'btn btn-success']);?></span>
		</div>
	</div>


</div>

</div>