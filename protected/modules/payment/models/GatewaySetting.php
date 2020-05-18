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

use yii\helpers\Inflector;

class GatewaySetting extends \app\components\SActiveRecord
{

    // Input types
    const KEY_TYPE_STRING = 0;

    const KEY_TYPE_BOOL = 1;

    const KEY_TYPE_INT = 2;

    const KEY_TYPE_EMAIL = 3;

    // Gateway types
    const GATEWAY_TYPE_PAYPAL = 0;

    const GATEWAY_TYPE_STRIPE = 1;

    const GATEWAY_TYPE_PAYTM = 2;

    const GATEWAY_TYPE_INSTAMOJO = 3;

    const GATEWAY_TYPE_AMAZON = 4;

    // transaction mode
    const MODE_TEST = 0;

    const MODE_LIVE = 1;

    public static function getModeOptions()
    {
        return [
            self::MODE_TEST => \Yii::t('app', 'Sandbox'),
            self::MODE_LIVE => \Yii::t('app', 'Production')
        ];
    }

    public static function getGatewayOptions()
    {
        return [
            self::GATEWAY_TYPE_PAYPAL => \Yii::t('app', 'Paypal'),
            self::GATEWAY_TYPE_STRIPE => \Yii::t('app', 'Stripe'),
            self::GATEWAY_TYPE_PAYTM => \Yii::t('app', 'PayTM'),
            self::GATEWAY_TYPE_INSTAMOJO => \Yii::t('app', 'InstaMojo'),
            self::GATEWAY_TYPE_AMAZON => \Yii::t('app', 'Amazon')
        ];
    }

    public static function getGateway($key)
    {
        $list = self::getGatewayOptions();
        return isset($list[$key]) ? $list[$key] : 'Not Defined';
    }

    public static function getGatewayKey($value)
    {
        $list = self::getGatewayOptions();
        return array_search($value, $list);
    }

    public static function gatewayFormFields()
    {
        return [
            self::GATEWAY_TYPE_PAYPAL => [
                'username' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'password' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'signature' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'client_id' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ],
                'secret_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ]
            ],
            self::GATEWAY_TYPE_STRIPE => [
                'secret_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'publishable_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ]
            ],
            self::GATEWAY_TYPE_PAYTM => [
                'merchant_id' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'merchant_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'Website' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'Channel' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => 'Web',
                    'required' => false
                ],
                'Industry Type' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => 'Retail',
                    'required' => false
                ]
            ],
            self::GATEWAY_TYPE_INSTAMOJO => [
                'private_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'private_token' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ],
                'salt' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ]
            ],
	    self::GATEWAY_TYPE_AMAZON => [
                'merchant_id' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'access_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'secret_key' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => true
                ],
                'client_id' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ],
                'client_secret' => [
                    'type' => self::KEY_TYPE_STRING,
                    'value' => '',
                    'required' => false
                ]
            ],
        ];
    }

    public static function generateField($key, $field)
    {
        $html = "";
        if (is_array($field)) {
            $required = (isset($field['required']) && ($field['required'] != false)) ? "required" : '';
            $value = isset($field['value']) ? $field['value'] : '';
            if (isset($field['type'])) {
                $html .= '<div class="form-group field-gateway-' . $key . '" ' . $required . '>';
                $html .= '<label class="control-label col-sm-3" for="gateway-' . $key . '">' . Inflector::titleize($key) . '</label><div class="col-sm-6">';
                
                switch ($field['type']) {
                    case self::KEY_TYPE_BOOL:
                        $html .= "<input type='checkbox' " . $required . " value='" . $value . "' class='form-control' name='Value[" . $key . "]' placeholder='" . Inflector::titleize($key) . "'>";
                        break;
                    case self::KEY_TYPE_STRING:
                        $html .= "<input type='text' " . $required . " value='" . $value . "' class='form-control' name='Value[" . $key . "]' placeholder='" . Inflector::titleize($key) . "'>";
                        break;
                    case self::KEY_TYPE_INT:
                        $html .= "<input type='number' " . $required . " value='" . $value . "' class='form-control' name='Value[" . $key . "]' placeholder='" . Inflector::titleize($key) . "'>";
                        break;
                    case self::KEY_TYPE_EMAIL:
                        $html .= "<input type='email' " . $required . " value='" . $value . "' name='Value[" . $key . "]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";
                        break;
                    default:
                        $html .= "<input type='text' " . $required . " value='" . $value . "' name='Value[" . $key . "]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";
                        break;
                }
            } else {
                $html .= '<div class="form-group field-gateway-' . $key . '" ' . $required . '>';
                $html .= '<label class="control-label col-sm-3 col-sm-3" for="gateway-' . $field . '">' . Inflector::titleize($key) . '</label><div class="col-sm-6">';
                $html .= "<input type='text' " . $required . " value='" . $value . "' name='Value[" . $key . "]' class='form-control' placeholder='" . Inflector::titleize($key) . "'>";
            }
            if ($required)
                $html .= '<div class="help-block help-block-error "></div>';
        } else {
            $html .= '<div class="form-group field-gateway-' . $field . '" required">';
            $html .= '<label class="control-label col-sm-3 col-sm-3" for="gateway-' . $field . '">' . Inflector::titleize($field) . '</label><div class="col-sm-6">';
            $html .= "<input type='text' class='form-control' name='Value[" . $field . "]' placeholder='" . Inflector::titleize($field) . "'>";
        }
        $html .= '</div></div>';
        return $html;
    }
}
