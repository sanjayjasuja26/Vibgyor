<?php

namespace app\modules\api2\components;

use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\Inflector;
use yii\rest\ActiveController;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\filters\ContentNegotiator;
use yii\web\Response;

/*
 * public static $httpStatuses = [
 * 100 => 'Continue',
 * 101 => 'Switching Protocols',
 * 102 => 'Processing',
 * 118 => 'Connection timed out',
 * 200 => 'OK',
 * 201 => 'Created',
 * 202 => 'Accepted',
 * 203 => 'Non-Authoritative',
 * 204 => 'No Content',
 * 205 => 'Reset Content',
 * 206 => 'Partial Content',
 * 207 => 'Multi-Status',
 * 208 => 'Already Reported',
 * 210 => 'Content Different',
 * 226 => 'IM Used',
 * 300 => 'Multiple Choices',
 * 301 => 'Moved Permanently',
 * 302 => 'Found',
 * 303 => 'See Other',
 * 304 => 'Not Modified',
 * 305 => 'Use Proxy',
 * 306 => 'Reserved',
 * 307 => 'Temporary Redirect',
 * 308 => 'Permanent Redirect',
 * 310 => 'Too many Redirect',
 * 400 => 'Bad Request',
 * 401 => 'Unauthorized',
 * 402 => 'Payment Required',
 * 403 => 'Forbidden',
 * 404 => 'Not Found',
 * 405 => 'Method Not Allowed',
 * 406 => 'Not Acceptable',
 * 407 => 'Proxy Authentication Required',
 * 408 => 'Request Time-out',
 * 409 => 'Conflict',
 * 410 => 'Gone',
 * 411 => 'Length Required',
 * 412 => 'Precondition Failed',
 * 413 => 'Request Entity Too Large',
 * 414 => 'Request-URI Too Long',
 * 415 => 'Unsupported Media Type',
 * 416 => 'Requested range unsatisfiable',
 * 417 => 'Expectation failed',
 * 418 => 'I\'m a teapot',
 * 421 => 'Misdirected Request',
 * 422 => 'Unprocessable entity',
 * 423 => 'Locked',
 * 424 => 'Method failure',
 * 425 => 'Unordered Collection',
 * 426 => 'Upgrade Required',
 * 428 => 'Precondition Required',
 * 429 => 'Too Many Requests',
 * 431 => 'Request Header Fields Too Large',
 * 449 => 'Retry With',
 * 450 => 'Blocked by Windows Parental Controls',
 * 451 => 'Unavailable For Legal Reasons',
 * 500 => 'Internal Server Error',
 * 501 => 'Not Implemented',
 * 502 => 'Bad Gateway or Proxy Error',
 * 503 => 'Service Unavailable',
 * 504 => 'Gateway Time-out',
 * 505 => 'HTTP Version not supported',
 * 507 => 'Insufficient storage',
 * 508 => 'Loop Detected',
 * 509 => 'Bandwidth Limit Exceeded',
 * 510 => 'Not Extended',
 * 511 => 'Network Authentication Required'
 * ];
 */

abstract class ApiTxController extends ActiveController {

    const API_OK = 200;
    const API_NOK = 400;

    public function behaviors() {
        $behaviors = parent::behaviors();
        // $behaviors['contentNegotiator'] = [
        // 'class' => ContentNegotiator::className(),
        // 'formats' => [
        // 'application/json' => Response::FORMAT_JSON
        // // 'application/xml' => Response::FORMAT_XML,
        // ]
        // ];
        // $behaviors['verbFilter'] = [
        // 'class' => VerbFilter::className(),
        // 'actions' => $this->verbs()
        // ];
        // $behaviors['rateLimiter'] = [
        // 'class' => \yii\filters\RateLimiter::className(),
        // ];
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::className(),
            'except' => [
                'index'
            ],
            'authMethods' => [
                HttpBasicAuth::className(),
                HttpBearerAuth::className(), // param : Authorization : Bearer L7b9G9n_jbw3oj8-G1X_t-Jg2FUNMcm1
                QueryParamAuth::className() // param : access-token
            ]
        ];

        return $behaviors;
    }

    /* Declare actions supported by APIs (Added in api/modules/v1/components/controller.php too) */

    public function actions() {
        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['index']);
        return $actions;
    }

    /* Declare methods supported by APIs */

    protected function verbs() {
        return [
            'create' => [
                'POST'
            ],
            'update' => [
                'PUT',
                'PATCH',
                'POST'
            ],
            'delete' => [
                'DELETE'
            ],
            'view' => [
                'GET'
            ],
            'index' => [
                'GET'
            ]
        ];
    }

    // For Pagination
    public $modelClass = '';
    protected $response = [];

    public function beforeAction($action) {
        return parent::beforeAction($action);
    }

    public function afterAction($action, $result) {
        $this->response['url'] = \yii::$app->request->pathInfo;

        if (isset($this->response['status'])) {
            \Yii::$app->response->setStatusCode($this->response['status'], '');
        } else {
            $this->response['status'] = self::API_NOK;
            \Yii::$app->response->setStatusCode(self::API_OK, "OK");
        }

        \Yii::$app->response->data = $this->response;
        return parent::afterAction($action, $result);
    }

    public function txDelete($id) {
        $model = $this->findModel($id);
        $data['status'] = self::API_NOK;
        if ($model->delete()) {
            $data['status'] = self::API_OK;
            $data['msg'] = $this->modelClass . ' is deleted Successfully.';
        }
        $this->response = $data;
    }

    public function txSave($fileAttributes = []) {
        $model = new $this->modelClass();
        if ($model->load(Yii::$app->request->post())) {
            foreach ($fileAttributes as $file) {
                $model->saveUploadedFile($model, $file);
            }
            if ($model->save()) {
                $data['status'] = self::API_OK;
                $data['detail'] = $model;
            } else {
                $err = '';
                foreach ($model->getErrors() as $error) {
                    $err .= implode(',', $error);
                }
                $data['error'] = $err;
            }
        }
        $this->response = $data;
    }

    public function txGet($id) {
        $model = $this->findModel($id);
        $data['detail'] = $model->asJson();
        $data['status'] = self::API_OK;
        $this->response = $data;
    }

    public function txIndex() {
        $model = new $this->modelClass();
        $dataProvider = $model->search(\Yii::$app->request->queryParams);
        $data = (new TPagination())->serialize($dataProvider);
        $data['status'] = self::API_OK;
        $this->response = $data;
    }

    protected function findModel($id) {
        $modelClass = Inflector::id2camel(\Yii::$app->controller->id);
        $modelClass = 'app\models\\' . $modelClass;
        if (($model = $modelClass::findOne($id)) !== null) {
            if (!($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
