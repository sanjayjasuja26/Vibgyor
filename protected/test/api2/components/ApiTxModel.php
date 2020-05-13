<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace app\modules\api2\components;

use yii\web\Link;
use app\components\TActiveRecord;

/**
 * Serializer converts resource objects and collections into array representation.
 *
 * Serializer is mainly used by REST controllers to convert different objects into array representation
 * so that they can be further turned into different formats, such as JSON, XML, by response formatters.
 *
 * The default implementation handles resources as [[Model]] objects and collections as objects
 * implementing [[DataProviderInterface]]. You may override [[serialize()]] to handle more types.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ApiTxModel extends TActiveRecord
{

    /**
     * Serializes a pagination into an array.
     *
     * @param
     *            $pagination
     * @return array the array representation of the pagination
     * @see addPaginationHeaders()
     */
    public static function pagination($dataProvider)
    {
        $pagination = $dataProvider->getPagination();
        if ($pagination !== false) {
            return [
                '_metadata' => [
                    '_links' => Link::serialize($pagination->getLinks(true)),
                    '_meta' => [
                        'totalCount' => $pagination->totalCount,
                        'pageCount' => $pagination->getPageCount(),
                        'currentPage' => $pagination->getPage() + 1,
                        'perPage' => $pagination->getPageSize()
                    ]
                ]
            ];
        }
        return [];
    }
}
