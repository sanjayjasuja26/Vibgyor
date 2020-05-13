<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */
namespace app\modules\api2\components;

use Yii;
use yii\base\Arrayable;
use yii\base\Model;
use yii\data\DataProviderInterface;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Link;

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
class TPagination extends Pagination
{

    /**
     *
     * @var string the name of the HTTP header containing the information about total number of data items.
     *      This is used when serving a resource collection with pagination.
     */
    public $totalCountHeader = 'X-Pagination-Total-Count';

    /**
     *
     * @var string the name of the HTTP header containing the information about total number of pages of data.
     *      This is used when serving a resource collection with pagination.
     */
    public $pageCountHeader = 'X-Pagination-Page-Count';

    /**
     *
     * @var string the name of the HTTP header containing the information about the current page number (1-based).
     *      This is used when serving a resource collection with pagination.
     */
    public $currentPageHeader = 'X-Pagination-Current-Page';

    /**
     *
     * @var string the name of the HTTP header containing the information about the number of data items in each page.
     *      This is used when serving a resource collection with pagination.
     */
    public $perPageHeader = 'X-Pagination-Per-Page';

    /**
     *
     * @var string the name of the envelope (e.g. `items`) for returning the resource objects in a collection.
     *      This is used when serving a resource collection. When this is set and pagination is enabled, the serializer
     *      will return a collection in the following format:
     *     
     *      ```php
     *      [
     *      'items' => [...], // assuming collectionEnvelope is "items"
     *      '_links' => { // pagination links as returned by Pagination::getLinks()
     *      'self' => '...',
     *      'next' => '...',
     *      'last' => '...',
     *      },
     *      '_meta' => { // meta information as returned by Pagination::toArray()
     *      'totalCount' => 100,
     *      'pageCount' => 5,
     *      'currentPage' => 1,
     *      'perPage' => 20,
     *      },
     *      ]
     *      ```
     *     
     *      If this property is not set, the resource arrays will be directly returned without using envelope.
     *      The pagination information as shown in `_links` and `_meta` can be accessed from the response HTTP headers.
     */
    public $collectionEnvelope = 'list';

    /**
     *
     * @var string the name of the envelope (e.g. `_links`) for returning the links objects.
     *      It takes effect only, if `collectionEnvelope` is set.
     * @since 2.0.4
     */
    public $linksEnvelope = '_links';

    /**
     *
     * @var string the name of the envelope (e.g. `_meta`) for returning the pagination object.
     *      It takes effect only, if `collectionEnvelope` is set.
     * @since 2.0.4
     */
    public $metaEnvelope = '_meta';

    /**
     *
     * @var Request the current request. If not set, the `request` application component will be used.
     */
    public $request;

    /**
     *
     * @var Response the response to be sent. If not set, the `response` application component will be used.
     */
    public $response;
    
     public $function = 'asJson';

    public $params = [];

    /**
     *
     * @var bool whether to preserve array keys when serializing collection data.
     *      Set this to `true` to allow serialization of a collection as a JSON object where array keys are
     *      used to index the model objects. The default is to serialize all collections as array, regardless
     *      of how the array is indexed.
     * @see serializeDataProvider()
     * @since 2.0.10
     */
    public $preserveKeys = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        if ($this->request === null) {
            $this->request = Yii::$app->getRequest();
        }
        if ($this->response === null) {
            $this->response = Yii::$app->getResponse();
        }
    }

    /**
     * Serializes the given data into a format that can be easily turned into other formats.
     * This method mainly converts the objects of recognized types into array representation.
     * It will not do conversion for unknown object types or non-object data.
     * The default implementation will handle [[Model]] and [[DataProviderInterface]].
     * You may override this method to support more object types.
     *
     * @param mixed $data
     *            the data to be serialized.
     * @return mixed the converted data.
     */
    public function serialize($data)
    {
        if ($data instanceof Model && $data->hasErrors()) {
            return $this->serializeModelErrors($data);
        } elseif ($data instanceof Arrayable) {
            return $this->serializeModel($data);
        } elseif ($data instanceof DataProviderInterface) {
            return $this->serializeDataProvider($data);
        } else {
            return $data;
        }
    }

    /**
     * Serializes a data provider.
     *
     * @param DataProviderInterface $dataProvider
     * @return array the array representation of the data provider.
     */
    protected function serializeDataProvider($dataProvider)
    {
        if ($this->preserveKeys) {
            $models = $dataProvider->getModels();
        } else {
            $models = array_values($dataProvider->getModels());
        }
        $models = $this->serializeModels($models);
        
        if (($pagination = $dataProvider->getPagination()) !== false) {
            $this->addPaginationHeaders($pagination);
        }
        
        $result = [
            $this->collectionEnvelope => $models
        ];
        if ($pagination !== false) {
            return array_merge($result, $this->serializePagination($pagination));
        } else {
            return $result;
        }
    }

    /**
     * Serializes a pagination into an array.
     *
     * @param Pagination $pagination
     * @return array the array representation of the pagination
     * @see addPaginationHeaders()
     */
    protected function serializePagination($pagination)
    {
        return [
            $this->linksEnvelope => Link::serialize($pagination->getLinks(true)),
            $this->metaEnvelope => [
                'totalCount' => $pagination->totalCount,
                'pageCount' => $pagination->getPageCount(),
                'currentPage' => $pagination->getPage() + 1,
                'perPage' => $pagination->getPageSize()
            ]
        ];
    }

    /**
     * Adds HTTP headers about the pagination to the response.
     *
     * @param Pagination $pagination
     */
    protected function addPaginationHeaders($pagination)
    {
        $links = [];
        foreach ($pagination->getLinks(true) as $rel => $url) {
            $links[] = "<$url>; rel=$rel";
        }
        
        $this->response->getHeaders()
            ->set($this->totalCountHeader, $pagination->totalCount)
            ->set($this->pageCountHeader, $pagination->getPageCount())
            ->set($this->currentPageHeader, $pagination->getPage() + 1)
            ->set($this->perPageHeader, $pagination->pageSize)
            ->set('Link', implode(', ', $links));
    }

    /**
     * Serializes a model object.
     *
     * @param Arrayable $model
     * @return array the array representation of the model
     */
    protected function serializeModel($model)
    {
        if ($this->request->getIsHead()) {
            return null;
        } else {
            list ($fields, $expand) = $this->getRequestedFields();
            return $model->toArray($fields, $expand);
        }
    }

    /**
     * Serializes the validation errors in a model.
     *
     * @param Model $model
     * @return array the array representation of the errors
     */
    protected function serializeModelErrors($model)
    {
        $this->response->setStatusCode(422, 'Data Validation Failed.');
        $result = [];
        foreach ($model->getFirstErrors() as $name => $message) {
            $result[] = [
                'field' => $name,
                'message' => $message
            ];
        }
        
        return $result;
    }

    /**
     * Serializes a set of models.
     *
     * @param array $models
     * @return array the array representation of the models
     */
        protected function serializeModels(array $models)
    {
        foreach ($models as $i => $model) {
            $class = get_class($model);
            if (method_exists($class, $this->function)) {
                if ($this->params)
                    $models[$i] = call_user_func_array(array(
                        $model,
                        $this->function
                    ), $this->params);
                else
                    $models[$i] = $model->{$this->function}();
            } else {
                $models[$i] = ArrayHelper::toArray($model);
            }
        }
        
        return $models;
    }
}
