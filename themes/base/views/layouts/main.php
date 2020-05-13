<?php
use app\assets\AppAsset;
use app\components\FlashMessage;
use app\modules\notification\widgets\NotificationWidget;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\Menu;

$user = Yii::$app->user->identity;

/* @var $this \yii\web\View */
/* @var $content string */
// $this->title = yii::$app->name;

AppAsset::register($this);
?>
<?php

$this->beginPage()?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language?>">

<head>
 <?php

$this->head()?>
<meta charset="<?=Yii::$app->charset?>" />
    <?=Html::csrfMetaTags()?>
     
   
<!-- Tell the browser to be responsive to screen width -->
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Favicon icon -->
<link rel="icon" type="image/png" sizes="16x16"
	href="<?=$this->theme->getUrl('assets/images/favicon.png')?>">
<title><?=Html::encode($this->title)?></title>

<!-- Custom CSS -->
<link href="<?=$this->theme->getUrl('css/style.css')?>" rel="stylesheet">
<link href="<?=$this->theme->getUrl('css/customStyle.css')?>"
	rel="stylesheet">
<link href="<?=$this->theme->getUrl('css/glyphicon.css')?>"
	rel="stylesheet">
<link href="<?=$this->theme->getUrl('css/font-awesome.css')?>"
	rel="stylesheet">
<!-- You can change the theme colors from here -->
<link href="<?=$this->theme->getUrl('css/colors/blue.css')?>" id="theme"
	rel="stylesheet">

<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
<![endif]-->
</head>

<body class="fix-header fix-sidebar card-no-border">
<?php

$this->beginBody();
?>
    <!-- ============================================================== -->
	<!-- Preloader - style you can find in spinners.css -->
	<!-- ============================================================== -->
	<div class="preloader">
		<svg class="circular" viewBox="25 25 50 50">
            <circle class="path" cx="50" cy="50" r="20" fill="none"
				stroke-width="2" stroke-miterlimit="10" /> </svg>
	</div>
	<!-- ============================================================== -->
	<!-- Main wrapper - style you can find in pages.scss -->
	<!-- ============================================================== -->
	<div id="main-wrapper">
		<!-- ============================================================== -->
		<!-- Topbar header - style you can find in pages.scss -->
		<!-- ============================================================== -->
		<header class="topbar">
			<nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
				<!-- ============================================================== -->
				<!-- Logo -->
				<!-- ============================================================== -->
				<div class="navbar-header">
					<a class="navbar-brand logo-hidden" href="<?=Url::home()?>"> <img
						src="<?=$this->theme->getUrl('img/logoblack.png')?>"
						alt="homepage" class="dark-logo" /></a> <a
						class="navbar-brand responsive-show" href="/tsaro-web-1181/"> <img
						src="<?=$this->theme->getUrl('img/logoblack-small.png')?>"
						alt="homepage" class="dark-logo" /></a>
				</div>
				<!-- ============================================================== -->
				<!-- End Logo -->
				<!-- ============================================================== -->
				<div class="navbar-collapse">
					<!-- ============================================================== -->
					<!-- toggle and nav items -->
					<!-- ============================================================== -->
					<ul class="navbar-nav mr-auto mt-md-0 ">
						<!-- This is  -->

						<li class="nav-item"><a
							class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark"
							href="javascript:void(0)"><i class="icon-arrow-left-circle"></i></a>
						</li>
						<?=NotificationWidget::widget()?> 
					</ul>
					<!-- ============================================================== -->
					<!-- User profile and search -->
					<!-- ============================================================== -->
					<ul class="navbar-nav my-lg-0">

						<li class="nav-item dropdown"><a
							class="nav-link dropdown-toggle text-muted waves-effect waves-dark"
							href="" data-toggle="dropdown" aria-haspopup="true"
							aria-expanded="false">                            
<?=$user->displayImage($user->profile_file, ['class' => 'profile-pic'], 'default.png', true);?>                                                       
                            </a>
							<div class="dropdown-menu dropdown-menu-right animated flipInY">
								<ul class="dropdown-user">
									<li>
										<div class="dw-user-box">
											<div class="u-img">
                                          <?=$user->displayImage($user->profile_file, [], 'default.png', true);?>
                                            </div>
											<div class="u-text">
												<h4><?=StringHelper::mb_ucfirst($user->full_name)?></h4>
												<p class="text-muted"><?=$user->email?></p>
												<a
													href="<?=Url::toRoute(['/user/view','id' => $user->id])?>"
													class="btn btn-rounded btn-danger btn-sm">View Profile</a>
											</div>
										</div>
									</li>
									<li role="separator" class="divider"></li>
									<li><a
										href="<?=Url::toRoute(['/user/changepassword','id' => $user->id]);?>"><i
											class="ti-wallet"></i> <?=Yii::t("app", 'Change Password')?></a></li>
									<li><a
										href="<?=Url::toRoute(['/user/update','id' => $user->id]);?>"><i
											class="ti-wallet"></i> <?=Yii::t("app", 'Update Profile')?></a></li>
									<li><a href="<?=Url::toRoute(['/email-queue/index']);?>"><i
											class="ti-email"></i> <?=Yii::t("app", 'Inbox')?></a></li>
									<li role="separator" class="divider"></li>
									<li><a href="<?=Url::toRoute(['/setting']);?>"><i
											class="ti-settings"></i> <?=Yii::t("app", 'Account Setting')?></a></li>
									<li role="separator" class="divider"></li>
									<li><a href="<?=Url::toRoute(['/user/logout']);?>"><i
											class="fa fa-power-off"></i> <?=Yii::t("app", 'Logout')?></a></li>
								</ul>
							</div></li>
						
						
						
						<?php

    echo \lajax\languagepicker\widgets\LanguagePicker::widget([
        'itemTemplate' => '<li class="nav-item dropdown"><a href="{link}" title="{language}"><i id="{language}"></i> {name}</a></li>',
        'activeItemTemplate' => '<a href="{link}" class="nav-link dropdown-toggle text-muted waves-effect waves-dark" title="{language}"><i id="{language}"></i> {name}</a>',
        'parentTemplate' => '<li class="language-picker nav-item dropdown dropdown-list {size}">{activeItem}<ul class="dropdown-menu dropdown-menu-right animated flipInY">{items}</ul></li>',
        'languageAsset' => 'lajax\languagepicker\bundles\LanguageLargeIconsAsset', // StyleSheets
        'languagePluginAsset' => 'lajax\languagepicker\bundles\LanguagePluginAsset' // JavaScripts
    ]);
    ?>	
					
					</ul>
				</div>
			</nav>
		</header>

		<!-- ============================================================== -->
		<!-- Left Sidebar - style you can find in sidebar.scss  -->
		<!-- ============================================================== -->
		<aside class="left-sidebar">
			<!-- Sidebar scroll-->
			<div class="scroll-sidebar">

				<!-- Sidebar navigation-->
                	<?php

                if (method_exists($this->context, 'renderNav')) {
                    ?>
                     <nav class="sidebar-nav">
			<?php

                    echo Menu::widget([
                        'encodeLabels' => false,
                        'activateParents' => true,
                        'items' => $this->context->renderNav(),
                        'options' => [
                            'class' => 'nav  nav-stacked side-navigation sidebar-nav',
                            'id' => 'sidebarnav'
                        ],
                        'submenuTemplate' => "\n<ul class='child-list'>\n{items}\n</ul>\n"
                    ]);
                    ?>
                    </nav>
	<?php
                }
                ?>
                <!-- End Sidebar navigation -->
			</div>
			<!-- End Sidebar scroll-->
			<!-- Bottom points-->
			<div class="sidebar-footer">
				<!-- items-->
				<a href="<?=Url::toRoute(['/setting'])?>" class="link"
					data-toggle="tooltip" title="Settings"><i class="ti-settings"></i></a>
				<a href="<?=Url::toRoute(['/email-queue/index'])?>" class="link"
					data-toggle="tooltip" title="Email"><i class="mdi mdi-gmail"></i></a>
				<a href="<?=Url::toRoute(['/user/logout'])?>" class="link"
					data-toggle="tooltip" title="Logout"><i class="mdi mdi-power"></i></a>

			</div>
			<!-- End Bottom points-->
		</aside>
		<!-- ============================================================== -->
		<!-- End Left Sidebar - style you can find in sidebar.scss  -->
		<!-- ============================================================== -->
		<!-- ============================================================== -->
		<!-- Page wrapper  -->
		<!-- ============================================================== -->

		<div class="page-wrapper">
			<div class="container-fluid">
				<div class="row ">
					<div class="col-sm-12">
						<div class="page-titles card">
							<div class="card-body">
								<h3 class="text-themecolor m-b-0 m-t-0"><?=StringHelper::mb_ucfirst(Yii::$app->controller->id)?></h3>
                        <?php

                        echo Breadcrumbs::widget([
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [
                                'class' => 'breadcrumb-item'
                            ]
                        ])?>

					</div>
						</div>
					</div>
				</div>
 
 <?php
if (yii::$app->hasModule('shadow')) {
    echo app\modules\shadow\components\ShadowWidget::widget();
}
?>	
			   <?=FlashMessage::widget(['type' => 'toster' /* 'position' => 'bottom-right' */])?>
			   			   
               <?=$content;?>
 
 </div>

			<footer class="footer"> <?=' &copy; ' . date('Y') . ' ' . Yii::$app->name . ' | All Rights Reserved ';?> Hosted by <a
					href="https://jiwebhosting.com/" target="_blank"> jiWebHosting.com
				</a>
			</footer>



		</div>
		<!-- ============================================================== -->
		<!-- End Page wrapper  -->
		<!-- ============================================================== -->
	</div>
	<!-- ============================================================== -->
	<!-- End Wrapper -->
	<!-- ============================================================== -->
	<!-- ============================================================== -->
	<!-- All Jquery -->
	<!-- ============================================================== -->
	<?php
yii\bootstrap4\Modal::begin([
    'headerOptions' => [
        'id' => 'modalHeader',
        'class' => 'modal-header'
    ],
    'id' => 'modal',
    'size' => 'modal-xl'
]);
echo "<div id='modalContent'><div style='text-align:center'><img src='" . $this->theme->getUrl('img/ttt.gif') . "'></div></div>";
yii\bootstrap4\Modal::end();
?>
<script>
$(document).on( 'click', '.showActionModalButton', function() {
	
	if ($('#modal').data('bs.modal').isShown) {
		$('#modal').find('#modalContent').load($(this).attr('value'));
	} else {
		// if modal isn't open; open it and load content
		$('#modal').modal('show').find('#modalContent').load(
				$(this).attr('value'));
		// dynamiclly set the header for the modal via title tag
		document.getElementById('modalHeader').innerHTML = '<h4 class="modal-title">'
			+ $(this).attr('title')
			+ '</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
	}
});
</script>

	<!-- Bootstrap tether Core JavaScript -->
	<script
		src="<?=$this->theme->getUrl('assets/plugins/bootstrap/js/tether.min.js')?>"></script>
	<!-- slimscrollbar scrollbar JavaScript -->
	<script src="<?=$this->theme->getUrl('js/jquery.slimscroll.js')?>"></script>
	<!--Wave Effects -->
	<script src="<?=$this->theme->getUrl('js/waves.js')?>"></script>
	<!--Menu sidebar -->
	<script src="<?=$this->theme->getUrl('js/sidebarmenu.js')?>"></script>
	<!--stickey kit -->
	<script
		src="<?=$this->theme->getUrl('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js')?>"></script>
	<!--Custom JavaScript -->
	<script src="<?=$this->theme->getUrl('js/custom.min.js')?>"></script>
	<script src="<?=$this->theme->getUrl('js/custom-modal.js')?>"></script>
	<script src="<?=$this->theme->getUrl('js/font-awesome-kit.js')?>"></script>
	<!-- ============================================================== -->
	<!-- Style switcher -->
	<!-- ============================================================== -->
	<script
		src="<?=$this->theme->getUrl('assets/plugins/styleswitcher/jQuery.style.switcher.js')?>"></script>
	
    <?php

    $this->endBody();
    ?>
</body>
<?php

$this->endPage()?>
</html>
