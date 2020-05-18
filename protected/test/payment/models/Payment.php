<?php

/**
 * This is the model class for table "tbl_payment_gateway".
 *
 * @property integer $id
 * @property string $title
 * @property string $value
 * @property integer $mode
 * @property integer $state_id
 * @property integer $type_id
 * @property string $created_on
 * @property string $updated_on
 * @property integer $created_by_id
 */

namespace app\modules\payment\models;

use app\models\EmailQueue;
use yii\helpers\Json;

class Payment extends \app\components\SActiveRecord {

    public $name = null;
    public $email = null;
    public $description;
    public $gateway = null;
    public $model_id = null;
    public $model_type = null;
    public $transaction_id = null;
    public $amount;
    public $currency = "USD";
    public $success = null;
    public $cancel = null;

    /*
     * Set these property for paypal
     * @property brandName

     * @property borderColor
     * @property landingPage //Billing — Non-PayPal account, Login — PayPal account login
     */
    public $options = [];

    public function rules() {
        return [
            [
                [
                    'amount',
                    'currency',
                    'description',
                ],
                'required'
            ],
            [
                [
                    'amount',
                ],
                'integer'
            ],
        ];
    }

    public function getAmount() {
        return sprintf("%.2f", $this->amount);
    }

    public function getCurrency() {
        return $this->currency;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getGateway() {
        return $this->gateway;
    }

    public function getOptions() {
        return $this->options;
    }

    public function requestData() {
        return $data = [
            'amount' => $this->getAmount(),
            'gateway' => $this->getGateway(),
            'currency' => $this->getCurrency(),
            'success' => $this->success,
            'description' => $this->getDescription(),
            'email' => $this->email,
            'name' => $this->name,
            'cancel' => $this->cancel,
            'options' => $this->getOptions()
        ];

        return $data;
    }

    public function checkTransaction() {
        $transaction = Transaction::find()->where([
                    'model_id' => $this->model_id,
                    'model_type' => $this->model_type
                ])->one();
        if (!empty($transaction)) {
            $this->gateway = ($transaction->gateway_type == null) ? $this->gateway : $transaction->gateway_type;
            $this->transaction_id = $transaction->id;
            $transaction->url = $this->getPaymentUrl();
            return $transaction;
        }
        return false;
    }

    public function createTransaction() {
        $transaction = new Transaction();
        if (empty($this->amount)) {
            throw new \InvalidArgumentException(\Yii::t('app', 'Amount not set'));
        }
        $transaction->amount = $this->getAmount();
        $transaction->currency = $this->getCurrency();
        $transaction->name = $this->name;
        $transaction->email = $this->email;
        $transaction->model_id = $this->model_id;
        $transaction->model_type = $this->model_type;
        $transaction->payment_status = Transaction::PAYMENT_STATUS_NEW;

        $transaction->value = Json::encode([
                    "request" => $this->requestData()
        ]);
        if ($transaction->save()) {
            $this->transaction_id = $transaction->id;
            $transaction->url = $this->getPaymentUrl();
            return $transaction;
        }
        throw new \InvalidArgumentException(\Yii::t('app', "Transaction not create {$transaction->errors}"));
    }

    public function getPaymentUrl() {
        $type = $this->gateway;

        if ($type !== null) {
            if (is_string($this->gateway))
                $type = GatewaySetting::getGatewayKey($this->gateway);

            switch ($type) {
                case GatewaySetting::GATEWAY_TYPE_PAYPAL:
                    $url = \Yii::$app->urlManager->createAbsoluteUrl([
                        '/payment/paypal/paynow',
                        'id' => $this->transaction_id
                    ]);
                    break;
                case GatewaySetting::GATEWAY_TYPE_STRIPE:
                    $url = \Yii::$app->urlManager->createAbsoluteUrl([
                        '/payment/stripe/paynow',
                        'id' => $this->transaction_id
                    ]);
                    break;
            }
            return $url;
        }
        return false;
    }

    public static function sendEmailToAdmins($transactionDetail) {
        $sub = "Payment {$transactionDetail->getState()}: {$transactionDetail->amount} {$transactionDetail->currency} ";
        $from = \Yii::$app->params['adminEmail'];
        $message = \yii::$app->view->renderFile('@app/modules/payment/mail/success.php', [
            'model' => $transactionDetail
        ]);
        EmailQueue::sendEmailToAdmins([
            'from' => $from,
            'subject' => $sub,
            'html' => $message
                ], true);
    }

}
