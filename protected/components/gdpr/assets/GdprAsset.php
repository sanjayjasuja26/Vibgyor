<?php
/**
 * @link https://github.com/yiimaker/yii2-social-share
 * @copyright Copyright (c) 2017 Yii Maker
 * @license BSD 3-Clause License
 */
namespace app\components\gdpr\assets;

use yii\web\AssetBundle;

/**
 * Asset for social icons font.
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class GdprAsset extends AssetBundle
{

    /**
     * @inheritdoc
     */
    public $sourcePath = '@app/components/gdpr/assets/src';

    /**
     * @inheritdoc
     */
    public $css = [
        'css/style-gdpr.css'
    ];
}
