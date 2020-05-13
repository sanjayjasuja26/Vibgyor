<?php

/**
 * @copyright Copyright (c) 2014 karpoff
 * @link https://github.com/karpoff/yii2-crop-image-upload
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace app\modules\media\assets;

use yii\web\AssetBundle;

class ImageUploadAsset extends AssetBundle
{

    public $sourcePath = '@app/modules/media/assets/src';

    public $baseUrl = '@web/media';

    public $js = [
        'js/jquery.fine-uploader.min.js'
    ];

    public $css = [
        'css/fine-uploader-new.min.css'
    ];

    public $jsOptions = [
        'position' => \yii\web\View::POS_HEAD
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\web\JqueryAsset'
    ];
}