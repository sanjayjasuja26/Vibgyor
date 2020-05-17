<?php

use app\assets\AppAsset;
use app\components\FlashMessage;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

    <head>
        <?php //$this->head() ?>
        <meta charset="<?= Yii::$app->charset ?>" />
        <?= Html::csrfMetaTags() ?>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="keywords" content="">
        <meta name="description" content="">
        <meta name="author" content="">

        <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
        <link rel="apple-touch-icon" href="images/apple-touch-icon.png">

        <!-- Google Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,400i,500,700,900" rel="stylesheet"> 
        <link href="https://fonts.googleapis.com/css?family=Droid+Serif:400,400i,700,700i" rel="stylesheet"> 

        <link rel="stylesheet" href="<?= $this->theme->getUrl('frontend/css/bootstrap.min.css'); ?>">
        <link rel="stylesheet" href="<?= $this->theme->getUrl('frontend/css/font-awesome.min.css'); ?>">
        <link rel="stylesheet" href="<?= $this->theme->getUrl('frontend/css/carousel.css'); ?>">
        <link rel="stylesheet" href="<?= $this->theme->getUrl('frontend/css/animate.css'); ?>">
        <link rel="stylesheet" href="<?= $this->theme->getUrl('frontend/style.css'); ?>">
        <!-- Favicon icon -->
        <title><?= Html::encode($this->title) ?></title>

    </head>

    <body>
        <?php $this->beginBody() ?>
        <!-- ============================================================== -->
        <!-- Preloader - style you can find in spinners.css -->
        <!-- ============================================================== -->
        <!-- LOADER -->
        <!--        <div id="preloader">
                    <img class="preloader" src="<?= $this->theme->getUrl('frontend/images/loader.gif'); ?>" alt="">
                </div> end loader -->
        <!-- END LOADER -->
        <div id="wrapper">
            <!-- BEGIN # MODAL LOGIN -->
            <div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Begin # DIV Form -->
                        <div id="div-forms">
                            <form id="login-form">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span class="flaticon-add" aria-hidden="true"></span>
                                </button>
                                <div class="modal-body">
                                    <input class="form-control" type="text" placeholder="What you are looking for?" required>
                                </div>
                            </form><!-- End # Login Form -->
                        </div><!-- End # DIV Form -->
                    </div>
                </div>
            </div>
            <!-- END # MODAL LOGIN -->

            <header class="header <?php echo (Yii::$app->controller->id != 'site' && Yii::$app->controller->action->id = 'index') ? 'header-normal' : ''; ?>">
                <div class="topbar clearfix">
                    <div class="container">
                        <div class="row-fluid">
                            <div class="col-md-6 col-sm-6 text-left">
                                <p>
                                    <strong><i class="fa fa-phone"></i></strong> +90 543 123 45 67 &nbsp;&nbsp;
                                    <strong><i class="fa fa-envelope"></i></strong> <a href="mailto:#">info@yoursite.com</a>
                                </p>
                            </div><!-- end left -->
                            <div class="col-md-6 col-sm-6 hidden-xs text-right">
                                <div class="social">
                                    <a class="facebook" href="#" data-tooltip="tooltip" data-placement="bottom" title="Facebook"><i class="fa fa-facebook"></i></a>              
                                    <a class="twitter" href="#" data-tooltip="tooltip" data-placement="bottom" title="Twitter"><i class="fa fa-twitter"></i></a>
                                    <a class="google" href="#" data-tooltip="tooltip" data-placement="bottom" title="Google Plus"><i class="fa fa-google-plus"></i></a>
                                    <a class="linkedin" href="#" data-tooltip="tooltip" data-placement="bottom" title="Linkedin"><i class="fa fa-linkedin"></i></a>
                                    <a class="pinterest" href="#" data-tooltip="tooltip" data-placement="bottom" title="Pinterest"><i class="fa fa-pinterest"></i></a>
                                </div><!-- end social -->
                            </div><!-- end left -->
                        </div><!-- end row -->
                    </div><!-- end container -->
                </div><!-- end topbar -->

                <div class="container">
                    <nav class="navbar navbar-default yamm">
                        <div class="navbar-header">
                            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                                <span class="sr-only">Toggle navigation</span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                                <span class="icon-bar"></span>
                            </button>
                            <div class="logo-normal">
                                <a class="navbar-brand" href="index.html"><h3>Vibgyor</h3>
                                </a>
                            </div>
                        </div>

                        <div id="navbar" class="navbar-collapse collapse">
                            <ul class="nav navbar-nav navbar-right">
                                <li><a href="index.html">Home</a></li>
                                <!--                                                                <li class="dropdown yamm-fw yamm-half"><a href="#" data-toggle="dropdown" class="dropdown-toggle active">Mega Menu <b class="fa fa-angle-down"></b></a>
                                                                                                    <ul class="dropdown-menu">
                                                                                                        <li>
                                                                                                            <div class="yamm-content clearfix">
                                                                                                                <div class="row-fluid">
                                                                                                                    <div class="col-md-6 col-sm-6">
                                                                                                                        <h4>Course Pages</h4>
                                                                                                                        <ul>
                                                                                                                            <li><a href="#">Courses Name 01</a></li>
                                                                                                                            <li><a href="#">Courses Name 02</a></li>
                                                                                                                            <li><a href="#">Courses Name 03</a></li>
                                                                                                                            <li><a href="#">Courses Name 04</a></li>
                                                                                                                            <li><a href="#">Courses Name 05</a></li>
                                                                                                                            <li><a href="#">Courses Name 06</a></li>
                                                                                                                            <li><a href="#">Courses Name 07</a></li>
                                                                                                                            <li><a href="#">Courses Name 08</a></li>
                                                                                                                            <li><a href="#">Courses Name 09</a></li>
                                                                                                                        </ul>
                                                                                                                    </div>
                                                                                                                    <div class="col-md-6 col-sm-6">
                                                                                                                        <div class="menu-widget text-center">
                                                                                                                            <div class="image-wrap entry">
                                                                                                                                <img src="<?= $this->theme->getUrl('frontend/upload/course_01.jpg'); ?>" alt="" class="img-responsive">
                                                                                                                                <div class="magnifier">
                                                                                                                                    <a href="#" title=""><i class="flaticon-add"></i></a>
                                                                                                                                </div>
                                                                                                                            </div> end image-wrap 
                                                                                                                            <h5><a href="#">Learning Bootstrap Framework</a></h5>
                                                                                                                            <small>$22.00</small>
                                                                                                                            <a href="#" class="menu-button">View Course</a>
                                                                                                                        </div> end widget 
                                                                                                                    </div>
                                                                                                                </div>
                                                                                                            </div>
                                                                                                        </li>
                                                                                                    </ul>
                                                                                                </li>-->
                                <li><a href="events.html">Events</a></li>
                                <li><a href="page-contact.html">Contact</a></li>
                                <li><a href="<?= Url::toRoute(['/user/signup']) ?>">Signup</a></li>
                                <li><a href="<?= Url::toRoute(['/user/login']) ?>">Login</a></li>
                                <li class="iconitem"><a href="#" data-toggle="modal" data-target="#login-modal"><i class="fa fa-search"></i></a></li>
                                <li class="iconitem"><a class="shopicon" href="shop-cart.html"><i class="fa fa-shopping-basket"></i> &nbsp;(0)</a></li>
                            </ul>
                        </div>
                    </nav><!-- end navbar -->
                </div><!-- end container -->
            </header>
            <?php
            if (Yii::$app->controller->id == 'site' && Yii::$app->controller->action->id == 'index') {
                ?>
                <section id="home" class="video-section js-height-full">
                    <div class="overlay"></div>
                    <div class="home-text-wrapper relative container">
                        <div class="home-message">
                            <p>Learning Management System</p>
                            <small>Edulogy is the ideal choice for your organization, your business and your online education system. Create your online course now with unlimited page templates, color options, and menu features.</small>
                            <div class="btn-wrapper">
                                <div class="text-center">
                                    <a href="#" class="btn btn-primary wow slideInLeft">Read More</a> &nbsp;&nbsp;&nbsp;<a href="#" class="btn btn-default wow slideInRight">Buy Now</a>
                                </div>
                            </div><!-- end row -->
                        </div>
                    </div>
                    <div class="slider-bottom">
                        <span>Explore <i class="fa fa-angle-down"></i></span>
                    </div>
                </section>
            <?php } ?>  
            <section id="wrapper">
                <?= FlashMessage::widget() ?>
                <?= $content ?>

            </section>


            <?= $this->render('footer.php'); ?>
            <script src="<?= $this->theme->getUrl('frontend/js/jquery.min.js') ?>"></script>
            <script src="<?= $this->theme->getUrl('frontend/js/bootstrap.min.js') ?>"></script>
            <!-- jQuery Files -->
            <script src="<?= $this->theme->getUrl('frontend/js/jquery.slimscroll.js') ?>"></script>

            <script src="<?= $this->theme->getUrl('frontend/js/carousel.js') ?>"></script>
            <script src="<?= $this->theme->getUrl('frontend/js/animate.js') ?>"></script>
            <script src="<?= $this->theme->getUrl('frontend/js/custom.js') ?>"></script>
            <!-- VIDEO BG PLUGINS -->
            <script src="<?= $this->theme->getUrl('frontend/js/videobg.js') ?>"></script>

            <?php $this->endBody() ?>
    </body>
    <?php $this->endPage() ?>
</html>