<?php
namespace app\modules\payment\controllers;

use function Composer\Autoload\includeFile;
includeFile(__DIR__ . "/../vendors/instamojo-php/src/Instamojo.php");

use app\components\TController;
use app\modules\payment\models\Gateway;
use app\modules\payment\models\GatewaySetting;
use app\modules\payment\models\Payment;
use app\modules\payment\models\Transaction;
use Exception;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `payment` module
 */
class InstamojoController extends TController
{

    public $enableCsrfValidation = false;

    public $gateway;

    public $config;

    /**
     * Renders the index view for the module
     *
     * @return string
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [
                            'success',
                            'paynow',
                            'cancel',
                            'webhook'
                        ],
                        'allow' => true,
                        'roles' => [
                            '@',
                            '*',
                            '?'
                        ]
                    ]
                ]
            ]
        ];
    }

    public function init()
    {
        $this->config = $this->getConfigParams();
        //VarDumper::dump($this->config);
        
        $this->gateway = new \Instamojo\Instamojo($this->config['private_key'], $this->config['private_token'], $this->config['testMode'] ? 'https://test.instamojo.com/api/1.1/' : null);
    }

    public function actionPaynow($id = null)
    {
        if (empty($this->config)) {
            $transaction = Transaction::findOne($id);
            if (! empty($transaction)) {
                $transaction->delete();
            }
            \Yii::$app->session->setFlash('error', \Yii::t('app', "Configartion not set."));
            return $this->redirect([
                '/'
            ]);
        }
        if (empty($id)) {
            $model = new Payment();
            $model->load(Yii::$app->request->post());
            $model->options = [
                "logoImageUrl" => \Yii::$app->view->theme->getUrl('/img/logo.png'),
                "brandName" => \Yii::$app->params['company']
            ];
            $transaction = new Transaction();
            
            $transaction->amount = $model->getAmount();
            $transaction->currency = $model->getCurrency();
            $transaction->description = $model->getDescription();
            $transaction->value = json_encode([
                'request' => $model->requestData()
            ]);
        } else {
            $transaction = Transaction::findOne($id);
        }
        $transaction->gateway_type = GatewaySetting::GATEWAY_TYPE_INSTAMOJO;
        $transaction->payment_status = Transaction::PAYMENT_STATUS_PENDING;
        if ($transaction->save()) {
            $options = $transaction->getPaymentResponse();
            $params = array_merge($this->config, array(
                'amount' => $transaction->amount,
                'currency' => $transaction->currency
            ));
            if (! empty($options) && ! empty($options['request']['options'])) {
                $params = array_merge($options['request']['options'], $params);
            }
            
            $params['webhook'] = \yii::$app->urlManager->createAbsoluteUrl([
                'payment/instamojo/webhook',
                'id' => $transaction->id
            ]);
            $params['returnUrl'] = \yii::$app->urlManager->createAbsoluteUrl([
                'payment/instamojo/success',
                'id' => $transaction->id
            ]);
            $response = null;
            try {
                $response = $this->gateway->paymentRequestCreate(array(
                    "purpose" => $transaction->description,
                    "amount" => $transaction->amount,
                    // "send_email" => true,
                    // "email" => "foo@example.com",
                    "redirect_url" => $params['returnUrl'],
                    "webhook" => $params['webhook']
                ));
                
                // print_r($response);
            } catch (Exception $e) {
                print('Error: ' . $e->getMessage());
                // $this->goBack();
            }
            
            if ($response != null && $transaction->save()) {
                
                $form = '<a href="REQUEST_URL" rel="im-checkout" data-behaviour="remote" data-style="light" data-text="Checkout With Instamojo"></a>
                  <script src="https://d2xwmjc4uy2hr5.cloudfront.net/im-embed/im-embed.min.js"></script>';
                
                // return str_replace ( 'REQUEST_URL',$response['longurl'], $form);
                return $this->redirect($response['longurl']);
                // payment was successful: update database
                \Yii::$app->session->setFlash('success', $response);
            }
        } else {
            \Yii::$app->session->setFlash('error', $transaction->getErrorsString());
        }
    }

    public function actionSuccess($id)
    {
        $paymentTransaction = Transaction::findOne($id);
        if (! empty($paymentTransaction)) {
            $sendData = json_decode($paymentTransaction->value, true);
            $payment_request_id = \yii::$app->request->getQueryParam('payment_request_id');
            $payment_id = \yii::$app->request->getQueryParam('payment_id');
            $response = null;
            try {
                $response = $api->paymentRequestPaymentStatus($payment_request_id, [
                    $payment_id
                ]);
                print_r($response['purpose']); // print purpose of payment request
                print_r($response['payment']['status']); // print status of payment
            } catch (Exception $e) {
                print('Error: ' . $e->getMessage());
            }
            
            \Yii::error(VarDumper::dump($response), '$response');
            if ($response['status'] == 'Credit') {
                $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_SUCCESS;
                $paymentTransaction->transaction_id = $response['mac'];
            } else {
                $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_FAIL;
                \Yii::$app->session->setFlash('error', $response['status']);
            }
            
            $paymentTransaction->value = json_encode([
                'response' => $response,
                'request' => $sendData['request']
            ]);
            $paymentTransaction->save();
            
            $successUrl = \Yii::$app->urlManager->createAbsoluteUrl([
                "/payment/default/success",
                'id' => $paymentTransaction->id
            ]);
            
            if (isset($sendData['request']['success'])) {
                $successUrl = $sendData['request']['success'];
                $query = parse_url($successUrl, PHP_URL_QUERY);
                // Returns a string if the URL has parameters or NULL if not
                if ($query) {
                    $successUrl .= "&transactionId=$paymentTransaction->id ";
                } else {
                    $successUrl .= "?transactionId=$paymentTransaction->id ";
                }
            }
            
            Payment::sendEmailToAdmins($paymentTransaction);
            
            return $this->redirect($successUrl);
        }
        \Yii::$app->session->setFlash('error', 'Transaction not complete.');
    }

    public function actionWebhook($id)
    {
        $paymentTransaction = Transaction::findOne($id);
        if (! empty($paymentTransaction) && $paymentTransaction->payment_status == Transaction::PAYMENT_STATUS_PENDING) {
            $sendData = json_decode($paymentTransaction->value, true);
            $payment_request_id = \yii::$app->request->post('payment_request_id');
            $payment_id = \yii::$app->request->post('payment_id');
            $status = \yii::$app->request->post('status');
            
            \Yii::error(VarDumper::dump(\yii::$app->request->post()), '$response');
            if ($status == 'Credit') {
                $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_SUCCESS;
                $paymentTransaction->transaction_id = \yii::$app->request->post('mac');
            }
            
            $paymentTransaction->value = json_encode([
                'response' => \yii::$app->request->post(),
                'request' => $sendData['request']
            ]);
            
            $paymentTransaction->save();
            
            Payment::sendEmailToAdmins($paymentTransaction);
        }
        return 'OK';
    }

    public function actionCancel($id)
    {
        $paymentTransaction = Transaction::findOne($id);
        $calcelUrl = \Yii::$app->urlManager->createAbsoluteUrl([
            "/"
        ]);
        if (! empty($paymentTransaction)) {
            $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_CANCEL;
            $paymentTransaction->save();
            $sendData = json_decode($paymentTransaction->value, true);
            if (isset($sendData['request']['cancel'])) {
                $calcelUrl = $sendData['request']['cancel'];
            }
        }
        
        return $this->redirect($calcelUrl);
    }

    protected function findModel($id)
    {
        if (($model = Gateway::findOne($id)) !== null) {
            if (! ($model->isAllowed()))
                throw new HttpException(403, Yii::t('app', 'You are not allowed to access this page.'));
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    public function getConfigParams()
    {
        $gateway = Gateway::findActive()->where([
            'state_id' => Gateway::STATE_ACTIVE,
            'type_id' => GatewaySetting::GATEWAY_TYPE_INSTAMOJO
        ])->one();
        $params = [];
        if (! empty($gateway)) {
            $gatewaySwtting = $gateway->gatewaySettings();
            $params['private_key'] = $gatewaySwtting['private_key'];
            $params['private_token'] = $gatewaySwtting['private_token'];
            $params['salt'] = $gatewaySwtting['salt'];
            $params['testMode'] = ($gateway->mode == GatewaySetting::MODE_TEST);
        }
        
        return $params;
    }

    protected function updateMenuItems($model = null)
    {}
}
