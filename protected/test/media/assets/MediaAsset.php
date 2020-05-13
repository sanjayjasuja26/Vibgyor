<?php
namespace app\modules\media\assets;

use yii\web\AssetBundle;

class MediaAsset extends AssetBundle
{

    /**
     *
     * @inheritdoc
     */
    public $sourcePath = '@app/modules/media/assets/src';

    /**
     *
     * @inheritdoc
     */
    public $css = [
        'css/style-media.css'
    ];

    public $js = [
        'js/custom-media.js'
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
