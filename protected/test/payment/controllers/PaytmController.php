<?php
namespace app\modules\payment\controllers;

use app\components\TController;
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
use yii\helpers\Url;

/**
 * Default controller for the `payment` module
 */
class PaytmController extends TController
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
        parent::init();
        
        $this->config = $this->getConfigParams();
        
        $PAYTM_STATUS_QUERY_NEW_URL = 'https://securegw-stage.paytm.in/merchant-status/getTxnStatus';
        $PAYTM_TXN_URL = 'https://securegw-stage.paytm.in/theia/processTransaction';
        if ($this->config['testMode']) {
            $PAYTM_STATUS_QUERY_NEW_URL = 'https://securegw.paytm.in/merchant-status/getTxnStatus';
            $PAYTM_TXN_URL = 'https://securegw.paytm.in/theia/processTransaction';
        }
        define('PAYTM_REFUND_URL', '');
        define('PAYTM_STATUS_QUERY_URL', $PAYTM_STATUS_QUERY_NEW_URL);
        define('PAYTM_STATUS_QUERY_NEW_URL', $PAYTM_STATUS_QUERY_NEW_URL);
        define('PAYTM_TXN_URL', $PAYTM_TXN_URL);
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
        $transaction->gateway_type = GatewaySetting::GATEWAY_TYPE_PAYTM;
        $transaction->payment_status = Transaction::PAYMENT_STATUS_PENDING;
        if ($transaction->save()) {
            
            $checkSum = "";
            $paramList = array();
            
            // Create an array having all required parameters for creating checksum.
            $paramList["MID"] = $this->config['merchant_id'];
            $paramList["ORDER_ID"] = $transaction->id;
            $paramList["CUST_ID"] = $transaction->id;
            $paramList["INDUSTRY_TYPE_ID"] = 'RETAIL';
            $paramList["CHANNEL_ID"] = 'Web';
            $paramList["TXN_AMOUNT"] = $transaction->amount;
            $paramList["WEBSITE"] = $this->config['Website'];
            $paramList['CALLBACK_URL'] = Url::to([
                'payment/paypal/success',
                'id' => $transaction->id
            ], true);
            
            /*$paramList["CALLBACK_URL"] = "http://localhost/PaytmKit/pgResponse.php";
             * $paramList["MSISDN"] = $MSISDN; //Mobile number of customer
             * $paramList["EMAIL"] = $EMAIL; //Email ID of customer
             * $paramList["VERIFIED_BY"] = "EMAIL"; //
             * $paramList["IS_USER_VERIFIED"] = "YES"; //
             *
             */
            
            // Here checksum string will return by getChecksumFromArray() function.
            $checkSum = getChecksumFromArray($paramList, $this->config['merchant_key']);
            return $this->render('form', [
                'checkSum' => $checkSum,
                'paramList' => $paramList
            ]);
        } else {
            \Yii::$app->session->setFlash('error', $transaction->getErrorsString());
        }
    }

    public function actionSuccess($id)
    {
        $paymentTransaction = Transaction::findOne($id);
        if (! empty($paymentTransaction)) {
            $sendData = json_decode($paymentTransaction->value, true);
            
            $paytmChecksum = "";
            $paramList = array();
            $isValidChecksum = "FALSE";
            
            $paramList = $_POST;
            $paytmChecksum = isset($_POST["CHECKSUMHASH"]) ? $_POST["CHECKSUMHASH"] : ""; // Sent by Paytm pg
                                                                                          
            // Verify all parameters received from Paytm pg to your application. Like MID received from paytm pg is same as your application’s MID, TXN_AMOUNT and ORDER_ID are same as what was sent by you to Paytm PG for initiating transaction etc.
            $isValidChecksum = verifychecksum_e($paramList, $this->config['merchant_key'], $paytmChecksum); // will return TRUE or FALSE string.
            
            if ($isValidChecksum == "TRUE") {
                echo "<b>Checksum matched and following are the transaction details:</b>" . "<br/>";
                if ($_POST["STATUS"] == "TXN_SUCCESS") {
                    echo "<b>Transaction status is success</b>" . "<br/>";
                    $paymentTransaction->transaction_id  = $_POST['TXN_ID'];
                    $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_SUCCESS;
                    // Process your transaction here as success transaction.
                    // Verify amount & order id received from Payment gateway with your application's order id and amount.
                } else {
                    echo "<b>Transaction status is failure</b>" . "<br/>";
                    $paymentTransaction->payment_status = Transaction::PAYMENT_STATUS_FAIL;
                }
                $paymentTransaction->value = json_encode([
                    'response' => $_POST,
                    'request' => $sendData['request']
                ]);
              /*   if (isset($_POST) && count($_POST) > 0) {
                    foreach ($_POST as $paramName => $paramValue) {
                        echo "<br/>" . $paramName . " = " . $paramValue;
                    }
                }
 */                \Yii::$app->session->setFlash('error', "Transaction status is failure");
            } else {
                echo "<b>Checksum mismatched.</b>";
                // Process transaction as suspicious.
            }
            
            $paymentTransaction->save();
            
            $successUrl = \Yii::$app->urlManager->createAbsoluteUrl([
                "default/success",
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
            'type_id' => GatewaySetting::GATEWAY_TYPE_PAYTM
        ])->one();
        $params = [];
        if (! empty($gateway)) {
            $gatewaySwtting = $gateway->gatewaySettings();
            $params['merchant_id'] = $gatewaySwtting['merchant_id'];
            $params['merchant_key'] = $gatewaySwtting['merchant_key'];
            $params['Website'] = $gatewaySwtting['Website'];
            $params['testMode'] = ($gateway->mode == GatewaySetting::MODE_TEST);
        }
        
        return $params;
    }

    protected function updateMenuItems($model = null)
    {}
}

function encrypt_e($input, $ky)
{
    $key = html_entity_decode($ky);
    $iv = "@@@@&&&&####$$$$";
    $data = openssl_encrypt($input, "AES-128-CBC", $key, 0, $iv);
    return $data;
}

function decrypt_e($crypt, $ky)
{
    $key = html_entity_decode($ky);
    $iv = "@@@@&&&&####$$$$";
    $data = openssl_decrypt($crypt, "AES-128-CBC", $key, 0, $iv);
    return $data;
}

function pkcs5_pad_e($text, $blocksize)
{
    $pad = $blocksize - (strlen($text) % $blocksize);
    return $text . str_repeat(chr($pad), $pad);
}

function pkcs5_unpad_e($text)
{
    $pad = ord($text{strlen($text) - 1});
    if ($pad > strlen($text))
        return false;
    return substr($text, 0, - 1 * $pad);
}

function generateSalt_e($length)
{
    $random = "";
    srand((double) microtime() * 1000000);
    
    $data = "AbcDE123IJKLMN67QRSTUVWXYZ";
    $data .= "aBCdefghijklmn123opq45rs67tuv89wxyz";
    $data .= "0FGH45OP89";
    
    for ($i = 0; $i < $length; $i ++) {
        $random .= substr($data, (rand() % (strlen($data))), 1);
    }
    
    return $random;
}

function checkString_e($value)
{
    if ($value == 'null')
        $value = '';
    return $value;
}

function getChecksumFromArray($arrayList, $key, $sort = 1)
{
    if ($sort != 0) {
        ksort($arrayList);
    }
    $str = getArray2Str($arrayList);
    $salt = generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash = hash("sha256", $finalString);
    $hashString = $hash . $salt;
    $checksum = encrypt_e($hashString, $key);
    return $checksum;
}

function getChecksumFromString($str, $key)
{
    $salt = generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash = hash("sha256", $finalString);
    $hashString = $hash . $salt;
    $checksum = encrypt_e($hashString, $key);
    return $checksum;
}

function verifychecksum_e($arrayList, $key, $checksumvalue)
{
    $arrayList = removeCheckSumParam($arrayList);
    ksort($arrayList);
    $str = getArray2StrForVerify($arrayList);
    $paytm_hash = decrypt_e($checksumvalue, $key);
    $salt = substr($paytm_hash, - 4);
    
    $finalString = $str . "|" . $salt;
    
    $website_hash = hash("sha256", $finalString);
    $website_hash .= $salt;
    
    $validFlag = "FALSE";
    if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
    } else {
        $validFlag = "FALSE";
    }
    return $validFlag;
}

function verifychecksum_eFromStr($str, $key, $checksumvalue)
{
    $paytm_hash = decrypt_e($checksumvalue, $key);
    $salt = substr($paytm_hash, - 4);
    
    $finalString = $str . "|" . $salt;
    
    $website_hash = hash("sha256", $finalString);
    $website_hash .= $salt;
    
    $validFlag = "FALSE";
    if ($website_hash == $paytm_hash) {
        $validFlag = "TRUE";
    } else {
        $validFlag = "FALSE";
    }
    return $validFlag;
}

function getArray2Str($arrayList)
{
    $findme = 'REFUND';
    $findmepipe = '|';
    $paramStr = "";
    $flag = 1;
    foreach ($arrayList as $key => $value) {
        $pos = strpos($value, $findme);
        $pospipe = strpos($value, $findmepipe);
        if ($pos !== false || $pospipe !== false) {
            continue;
        }
        
        if ($flag) {
            $paramStr .= checkString_e($value);
            $flag = 0;
        } else {
            $paramStr .= "|" . checkString_e($value);
        }
    }
    return $paramStr;
}

function getArray2StrForVerify($arrayList)
{
    $paramStr = "";
    $flag = 1;
    foreach ($arrayList as $key => $value) {
        if ($flag) {
            $paramStr .= checkString_e($value);
            $flag = 0;
        } else {
            $paramStr .= "|" . checkString_e($value);
        }
    }
    return $paramStr;
}

function redirect2PG($paramList, $key)
{
    $hashString = getchecksumFromArray($paramList);
    $checksum = encrypt_e($hashString, $key);
}

function removeCheckSumParam($arrayList)
{
    if (isset($arrayList["CHECKSUMHASH"])) {
        unset($arrayList["CHECKSUMHASH"]);
    }
    return $arrayList;
}

function getTxnStatus($requestParamList)
{
    return callAPI(PAYTM_STATUS_QUERY_URL, $requestParamList);
}

function getTxnStatusNew($requestParamList)
{
    return callNewAPI(PAYTM_STATUS_QUERY_NEW_URL, $requestParamList);
}

function initiateTxnRefund($requestParamList)
{
    $CHECKSUM = getRefundChecksumFromArray($requestParamList, PAYTM_MERCHANT_KEY, 0);
    $requestParamList["CHECKSUM"] = $CHECKSUM;
    return callAPI(PAYTM_REFUND_URL, $requestParamList);
}

function callAPI($apiURL, $requestParamList)
{
    $jsonResponse = "";
    $responseParamList = array();
    $JsonData = json_encode($requestParamList);
    $postData = 'JsonData=' . urlencode($JsonData);
    $ch = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ));
    $jsonResponse = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}

function callNewAPI($apiURL, $requestParamList)
{
    $jsonResponse = "";
    $responseParamList = array();
    $JsonData = json_encode($requestParamList);
    $postData = 'JsonData=' . urlencode($JsonData);
    $ch = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json',
        'Content-Length: ' . strlen($postData)
    ));
    $jsonResponse = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}

function getRefundChecksumFromArray($arrayList, $key, $sort = 1)
{
    if ($sort != 0) {
        ksort($arrayList);
    }
    $str = getRefundArray2Str($arrayList);
    $salt = generateSalt_e(4);
    $finalString = $str . "|" . $salt;
    $hash = hash("sha256", $finalString);
    $hashString = $hash . $salt;
    $checksum = encrypt_e($hashString, $key);
    return $checksum;
}

function getRefundArray2Str($arrayList)
{
    $findmepipe = '|';
    $paramStr = "";
    $flag = 1;
    foreach ($arrayList as $key => $value) {
        $pospipe = strpos($value, $findmepipe);
        if ($pospipe !== false) {
            continue;
        }
        
        if ($flag) {
            $paramStr .= checkString_e($value);
            $flag = 0;
        } else {
            $paramStr .= "|" . checkString_e($value);
        }
    }
    return $paramStr;
}

function callRefundAPI($refundApiURL, $requestParamList)
{
    $jsonResponse = "";
    $responseParamList = array();
    $JsonData = json_encode($requestParamList);
    $postData = 'JsonData=' . urlencode($JsonData);
    $ch = curl_init($apiURL);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_URL, $refundApiURL);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    $jsonResponse = curl_exec($ch);
    $responseParamList = json_decode($jsonResponse, true);
    return $responseParamList;
}