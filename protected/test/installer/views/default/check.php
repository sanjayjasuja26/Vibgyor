
<div class="panel panel-default">
	<div class="panel-heading">
		<h2 class="text-center">System Check</h2>
	</div>

	<div class="panel-body">
		<p>In the following overview, you can see, if all the requirements are
			ready.</p>

		<hr />
		<p>
			Please make sure you have the database file in backup folder with
			name <strong>db/install.sql</strong>.
		</p>
		<div class="prerequisites-list">
			<ul>
				<li>
						<?php
						if (is_array($this->context->module->sqlfile)){
						    foreach ($this->context->module->sqlfile as $sql){
						        if (file_exists($sql)){
						            $db = 'OK';
						        }else{ 
						            $db = 'NOK';
						            break;
						        }
						    }
						}
						elseif (file_exists ( $this->context->module->sqlfile )) {
							$db = 'OK';
							?>
							<i class="fa fa-check-circle check-ok animated bounceIn"></i>
						<?php
						} else {
							$db = 'Not OK';
							$hasError = "DB file missing";
							?>
							<i class="fa fa-minus-circle check-error animated wobble"></i>
						<?php
						}
						?>

						<strong>DB File</strong> : <?= $db?>

					
					</li>
				<?php
				foreach ( $checks as $check ) {
					?>
					<li>
						<?php
					
					if ($check ['error']) {
						?>
							<i class="fa fa-minus-circle check-error animated wobble"></i>
						<?php
					} elseif ($check ['warning']) {
						?>
							<i
					class="fa fa-exclamation-triangle check-warning animated swing"></i>
						<?php
					} else{
						?>
							<i class="fa fa-check-circle check-ok animated bounceIn"></i>
						<?php
					}
					?>

						<strong><?= $check['name']; ?></strong> :				 <?= $check ['error'] ? 'NOK' :'OK'?>

						<?php if (isset($check['memo'])) { ?>
							<span>(Hint: <?= $check['memo']; ?>)</span>
						<?php } ?>
					</li>
				<?php
				}
				?>
			</ul>
		</div>

		<?php
		
		if (! $hasError) {
			?>
			<div class="alert alert-success">Congratulations! Everything is ok
			and ready to start over!</div>
		<?php
		}
		?>
		<hr />

		<?= \yii\helpers\Html::a('<i class="fa fa-repeat"></i> ' . 'Check again', ['default/go'], ['class' => 'btn btn-info'])?>

		<?php if (!$hasError) { ?>
			<?= \yii\helpers\Html::a('Next' . ' <i class="fa fa-arrow-circle-right"></i>', ['default/step2'], ['class' => 'btn btn-primary'])?>
		<?php } ?>
	</div>
</div>