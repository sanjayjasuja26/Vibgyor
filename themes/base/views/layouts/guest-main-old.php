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
        <?php $this->head() ?>
        <meta charset="<?= Yii::$app->charset ?>" />
        <?= Html::csrfMetaTags() ?>
        <!-- Tell the browser to be responsive to screen width -->
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!-- Favicon icon -->
        <link rel="icon" type="image/png" sizes="16x16"
              href="../assets/images/favicon.png">
        <title><?= Html::encode($this->title) ?></title>


        <!-- Custom CSS -->
        <link href="<?= $this->theme->getUrl('css/style.css') ?>" rel="stylesheet">
        <link href="<?= $this->theme->getUrl('css/customStyle.css') ?>"
              rel="stylesheet">
        <link href="<?= $this->theme->getUrl('css/glyphicon.css') ?>"
              rel="stylesheet">
        <!-- You can change the theme colors from here -->
        <link href="<?= $this->theme->getUrl('css/colors/blue.css') ?>" id="theme"
              rel="stylesheet">
        <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
        <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
            <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->
    </head>

    <body>
        <?php $this->beginBody() ?>
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
        <!-- ADD HEADER -->
        <nav
            class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light ftco-navbar-light-2
            "
            id="ftco-navbar">
            <div class="container">
                <a class="navbar-brand" href="<?= Url::home(); ?>"><img
                        src="<?= $this->theme->getUrl('img/dummylogo.png') ?>"></a>
                <button class="navbar-toggler" type="button" data-toggle="collapse"
                        data-target="#ftco-nav" aria-controls="ftco-nav"
                        aria-expanded="false" aria-label="Toggle navigation ">
                    <span class="oi oi-menu "> </span>Menu
                </button>
                <div class="collapse navbar-collapse " id="ftco-nav">
                    <ul class="navbar-nav ml-auto ">
                        <li class="nav-item active "><a href="<?= Url::home(); ?>" class="nav-link">Home </a></li>
                        <li class="nav-item"><a href="<?= Url::toRoute(['/site/about']) ?> " class="nav-link">About
                                Us </a></li>
                        <li class="nav-item dropdown "><a class="nav-link dropdown-toggle "
                                                          href="shop.php " id="dropdown04" data-toggle="dropdown"
                                                          aria-haspopup="true" aria-expanded="false">Pages</a>
                            <div class="dropdown-menu" aria-labelledby="dropdown04">
                                <a class="dropdown-item" href="<?= Url::toRoute(['/site/privacy']) ?>">Privacy </a> <a
                                    class="dropdown-item" href="<?= Url::toRoute(['/site/terms']) ?>">Terms </a>
                            </div></li>

                        <li class="nav-item"><a href="<?= Url::toRoute(['/site/contact']) ?>" class="nav-link">Contact Us</a></li>
                        <li class="nav-item cta cta-colored "><a
                                href="<?= Url::toRoute(['/user/login']) ?>" class="nav-link">Log In
                                <i class="fas fa-sign-in-alt "> </i>
                            </a></li>
                    </ul>
                </div>
            </div>
        </nav>
        <section id="wrapper">
            <?= FlashMessage::widget() ?>
            <?= $content ?>

        </section>


        <?= $this->render('footer.php'); ?>



        <!-- ADD FOOTER -->

        <!-- ============================================================== -->
        <!-- End Wrapper -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- All Jquery -->
        <!-- ============================================================== -->
        <script
        src="<?= $this->theme->getUrl('assets/plugins/bootstrap/js/tether.min.js') ?>"></script>
        <!-- slimscrollbar scrollbar JavaScript -->
        <script src="<?= $this->theme->getUrl('js/jquery.slimscroll.js') ?>"></script>
        <!--Wave Effects -->
        <script src="<?= $this->theme->getUrl('js/waves.js') ?>"></script>
        <!--Menu sidebar -->
        <script src="<?= $this->theme->getUrl('js/sidebarmenu.js') ?>"></script>
        <!--stickey kit -->
        <script
        src="<?= $this->theme->getUrl('assets/plugins/sticky-kit-master/dist/sticky-kit.min.js') ?>"></script>
        <!--Custom JavaScript -->
        <script src="<?= $this->theme->getUrl('js/custom.min.js') ?>"></script>
        <!-- ============================================================== -->
        <!-- Style switcher -->
        <!-- ============================================================== -->
        <script
        src="<?= $this->theme->getUrl('assets/plugins/styleswitcher/jQuery.style.switcher.js') ?>"></script>


        <?php $this->endBody() ?>
    </body>
    <?php $this->endPage() ?>
</html>