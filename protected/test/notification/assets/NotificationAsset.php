<?php
namespace app\modules\notification\assets;

use yii\web\AssetBundle;

class NotificationAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/modules/notification/assets/src';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/style-notification.css'
    ];

    public $js = ['js/notify.js'];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'
    ];
}
