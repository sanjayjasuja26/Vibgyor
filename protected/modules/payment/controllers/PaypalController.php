<?php
namespace app\modules\payment\controllers;

use Omnipay\Omnipay;
use app\components\SController;
use app\modules\payment\models\Gateway;
use app\modules\payment\models\GatewaySetting;
use app\modules\payment\models\Payment;
use app\modules\payment\models\Transaction;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * Default controller for the `payment` module
 */
class PaypalController extends SController
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
                            'cancel'
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
        $this->gateway = Omnipay::create('PayPal\Express');
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
            $transaction->value = Json::encode([
                'request' => $model->requestData()
            ]);
        } else {
            $transaction = Transaction::findOne($id);
        }
        $transaction->gateway_type = GatewaySetting::GATEWAY_TYPE_PAYPAL;
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
            
            $params['cancelUrl'] = \yii::$app->urlManager->createAbsoluteUrl([
                'payment/paypal/cancel',
                'id' => $transaction->id
            ]);
            $params['returnUrl'] = \yii::$app->urlManager->createAbsoluteUrl([
                'payment/paypal/success',
                'id' => $transaction->id
            ]);
            
            $response = $this->gateway->purchase($params)->send();
            
            if ($response->isRedirect()) {
                // redirect to offsite payment gateway
                $response->getTransactionReference();
                $response->redirect();
            } elseif ($response->isSuccessful()) {
                // payment was successful: update database
                 \Yii::$app->session->setFlash('success', $response->getMessage());
            } else {
                // payment failed: display message to customer
                 \Yii::$app->session->setFlash('error', $response->getMessage());
            }
        } else {
            \Yii::$app->session->setFlash('error', $transaction->getErrorsString());
        }
    }

    public function actionSuccess($id)
    {
        $paymentTransaction = Transaction::findOne($id);
        if (! empty($paymentTransaction)) {
            $paymentTransaction->payer_id = \yii::$app->request->getQueryParam('PayerID');
            $sendData = json_decode($paymentTransaction->value, true);
            
            $params = array_merge($this->config, array(
                'amount' => $paymentTransaction->amount,
                'currency' => $paymentTransaction->currency
            ));
            $response = $this->gateway->completePurchase(array_merge($params, array(
                'transactionReference' => \yii::$app->request->getQueryParam('token'),
                'amount' => $paymentTransaction->amount,
                'currency' => $paymentTransaction->currency
            )))
                ->send();
            
            $transactionDetail = $response->getData();
            
           // \Yii::error(VarDumper::dump($transactionDetail), 'transactionDetail');
            if ($transactionDetail['ACK'] == 'Success') {
                $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_SUCCESS;
                $paymentTransaction->transaction_id = $transactionDetail['PAYMENTINFO_0_TRANSACTIONID'];
            } else if ($transactionDetail['ACK'] == 'Failure') {
                $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_FAIL;
            } else {
                \Yii::$app->session->setFlash('error', $transactionDetail['L_LONGMESSAGE0']);
            }
            
            $paymentTransaction->value = json_encode([
                'response' => $transactionDetail,
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
            'type_id' => GatewaySetting::GATEWAY_TYPE_PAYPAL
        ])->one();
        $params = [];
        if (! empty($gateway)) {
            $gatewaySwtting = $gateway->gatewaySettings();
            $params['username'] = $gatewaySwtting['username'];
            $params['password'] = $gatewaySwtting['password'];
            $params['signature'] = $gatewaySwtting['signature'];
            $params['testMode'] = ($gateway->mode == GatewaySetting::MODE_TEST);
        }
        
        return $params;
    }

    protected function updateMenuItems($model = null)
    {}
}
