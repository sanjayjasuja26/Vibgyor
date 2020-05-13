<?php
use yii\helpers\Url;

/**
 *
 * @copyright : Amusoftech Pvt. Ltd. < http://amusoftech.com/ >
 * @author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
/* @var $this yii\web\View */
$this->title = Yii::$app->name;

?>


<div class="hero-wrap js-fullheight a fullheight" style="background-image:url(<?=$this->theme->getUrl('assets/images/background/login-register.jpg ')?>);">
	<div class="overlay"></div>
	<div class="inner-section nofixed">
		<div class="container">
			<div
				class="row no-gutters slider-text js-fullheight align-items-center justify-content-center">
				<div class="col-md-11 ftco-animate text-center">
					<h1>Welcome to Amusoftech</h1>
					<h2>
						<span>Nec natum feugait atomorum in. Ad vis suavitate adipiscing,
							nec ex suscipit adipiscing. Ornatus repudiare vix ei, labores
							recusabo vis ut.</span>
					</h2>
				</div>
				<div class="mouse">
					<a href="#" class="mouse-icon">
						<div class="mouse-wheel">
							<span class="ion-ios-arrow-down"> </span>
						</div>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
<section class="white-bg">
	<div class="container">
		<div class="row">
			<div class="col-sm-8 section-heading ">
				<h5 class="default-color text-uppercase mt-0 ">Build Your Own</h5>
				<h2 class="font-700 mt-10 ">The Best Way to Sell Your Design .</h2>
				<hr class="dark-bg center_line bold-line ">
				<h4>Euismod incorrupte mel id. Est ne gloriatur persequeris, sea
					iisque legendos sadipscing in, adipisci erroribus nec id.</h4>
			</div>
		</div>
		<div class="row mt-50 ">
			<div class="col-md-4 col-sm-4 col-xs-12">
				<div
					class="feature-box text-center mb-50 feature-box-rounded wow fadeInUp center-feature"
					data-wow-delay="0.1s "
					style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
					<span class="font-100px default-color font-700 "> <span
						class="dark-color"> 0</span> 1
					</span>
					<h4 class="mt-0 font-600 ">Unique Element</h4>
					<p class="font-400">Lorem Ipsum is simply dummy text of the
						printing and typesetting industry .</p>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12 ">
				<div
					class="feature-box text-center mb-50 feature-box-rounded wow fadeInUp center-feature"
					data-wow-delay="0.2s "
					style="visibility: visible; animation-delay: 0.2s; animation-name: fadeInUp;">
					<span class="font-100px default-color font-700 "> <span
						class="dark-color"> 0</span> 2
					</span>
					<h4 class="mt-0 font-600 ">Fully Responsive</h4>
					<p class="font-400">Lorem Ipsum is simply dummy text of the
						printing and typesetting industry .</p>
				</div>
			</div>
			<div class="col-md-4 col-sm-4 col-xs-12 ">
				<div
					class="feature-box text-center mb-50 feature-box-rounded wow fadeInUp center-feature"
					data-wow-delay="0.3s "
					style="visibility: visible; animation-delay: 0.3s; animation-name: fadeInUp;">
					<span class="font-100px default-color font-700 "> <span
						class="dark-color"> 0</span> 3
					</span>
					<h4 class="mt-0 font-600 ">Modern Design</h4>
					<p class="font-400">Lorem Ipsum is simply dummy text of the
						printing and typesetting industry .</p>
				</div>
			</div>
		</div>
	</div>
</section>
<script>
$(document).ready(function() {
	try {
		$('.a').ripples({
			resolution: 200,
			perturbance: 0.01
		});
	}
	catch (e) {
		$('.error').show().text(e);
	}
});
</script>
<script
	src="http://www.jqueryscript.net/demo/jQuery-Plugin-For-Water-Ripple-Animation-ripples/js/jquery.ripples.js"></script>