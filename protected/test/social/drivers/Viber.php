<?php
/**
 * @link https://github.com/yiimaker/yii2-social-share
 * @copyright Copyright (c) 2017 Yii Maker
 * @license BSD 3-Clause License
 */
namespace app\modules\social\drivers;


use app\modules\social\socialshare\base\AbstractDriver;

/**
 * Driver for Viber messenger.
 *
 * @link https://viber.com
 *
 * WARNING: This driver works only in mobile devices
 * with installed Viber client.
 *
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 1.0
 */
class Viber extends AbstractDriver
{
    /**
     * @inheritdoc
     */
    protected function processShareData()
    {
        $this->url = static::encodeData($this->url);
    }

    /**
     * @inheritdoc
     */
    protected function buildLink()
    {
        return 'viber://forward?text={url}';
    }
}
