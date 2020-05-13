<?php
namespace app\modules\social\drivers;

use app\modules\social\socialshare\base\AbstractMailDriver;

/**
 * Driver for Yahoo.
 *
 * @link https://www.yahoo.com
 *      
 * @author Vladimir Kuprienko <vldmr.kuprienko@gmail.com>
 * @since 2.0
 */
class Yahoo extends AbstractMailDriver
{

    /**
     * @inheritdoc
     */
    protected function buildLink()
    {
        return 'https://compose.mail.yahoo.com/?subject={title}&body={body}';
    }
}
