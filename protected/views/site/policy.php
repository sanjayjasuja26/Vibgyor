<?php
/**
 *
 * @copyright : Amusoftech Pvt. Ltd. < http://amusoftech.com/ >
 * @author	 :Ram Mohamad Singh <  er.amudeep@gmail.com >
 */
use app\modules\page\models\Page;
use bizley\contenttools\ContentTools;
use yii\helpers\Url;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\modules\page\models\Page; */
/* @var $model Page */

/*
 * $this->title = 'About';
 * $this->params ['breadcrumbs'] [] = $this->title;
 *
 */
?>

<div class="page--header pt--120 pb--120 hero-wrap text-center topbanner-back bg--img" style="background-image:url(<?=$this->theme->getUrl('assets/images/background/login-register.jpg')?>);">
	<div class="container">
		<div class="title">

			<h2 class="h1 text-white">Privacy Policy</h2>
		</div>

		<ul class="breadcrumb text-gray ff--primary">
			<li><a href="index.php" class="btn-link">Home</a></li>

			<li class="active"><span class="text-primary">Privacy Policy</span></li>
		</ul>
	</div>
</div>

<div class="site-about pt--60 pb--60">
	<div class="container">
    <?php
    if (User::isManager()) {
       
        $url = \yii\helpers\Url::toRoute([
            '/page/page/save-content',
            'id' => ((! empty($model)) ? $model->id : ""),
            'title' => "Privacy & Policy",
            'type' => Page::TYPE_PRIVACY
        ]);

        ContentTools::begin([

            'saveEngine' => [
                'save' => $url
            ],
            'imagesEngine' => [
                'upload' => Url::toRoute([
                    '/site/content-tools-image-upload'
                ]),
                'rotate' => Url::toRoute([
                    '/site/content-tools-image-rotate'
                ]),
                'insert' => Url::toRoute([
                    '/site/content-tools-image-insert'
                ])
            ]
        ]);
    }
    ?>
        <?php

        if (! empty($model)) :
            ?>
            <?=$model->description?>
        <?php

        else :
            ?>
            <h2>This is heading example</h2>
		<p>Here is the default text that can be changed using
			yii2-content-tools.</p>
        <?php

        endif;
        ?>
    <?php
    if (User::isManager()) {
        ContentTools::end();
    }

    ?>
</div>
</div>

