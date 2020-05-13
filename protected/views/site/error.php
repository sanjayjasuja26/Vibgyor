<?php
/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;

?>

<section class="breadcrumb_area">
	<div class="overlay bg-parallax" data-stellar-ratio="0.8"
		data-stellar-vertical-offset="0" data-background=""></div>
	<div class="container">
		<div class="page-cover text-center">
			<h2 class="page-cover-tittle">Error</h2>
		</div>
	</div>
</section>
<section class="login-form section_gap">
	<div class="container">
		<div class="row">
			<div class="offset-md-2 col-md-8">
				<div class="card card-view mb-0">
					<div class="card-wrapper collapse in">
						<div class="card-body">
							<div class="text-center">
								<a href="index.php"> <img
									src="<?php echo $this->theme->getUrl("img/error.png")?>"></a>
							</div>
							<hr>
							<div class="row">
								<div class="col-sm-12 col-xs-12 text-center">
									<h3 class="mb-20 text-danger"><?= $name?></h3>
									<a
										class="btn btn-success btn-rounded green-btn btn-icon   right-icon  mt-30"
										href="<?= Yii::$app->homeUrl ?>"><span>Back to Home</span> <i
										class="fa fa-space-shuttle"></i></a>

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>