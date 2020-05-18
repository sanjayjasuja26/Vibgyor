<?php

namespace app\components;

use Yii;
use yii\mail\BaseMailer;
use yii\base\InvalidConfigException;
use yii\log\Logger;

class SMailer extends BaseMailer {

    /**
     *
     * @var string message default class name.
     */
    public $messageClass = 'yii\swifSMailer\Message';

    /**
     *
     * @var bool whether to enable writing of the SwifSMailer internal logs using Yii log mechanism.
     *      If enabled [[Logger]] plugin will be attached to the [[transport]] for this purpose.
     * @see Logger
     */
    public $enableSwifSMailerLogging = false;

    /**
     *
     * @var \Swift_Mailer Swift mailer instance.
     */
    private $_swifSMailer;

    /**
     *
     * @var \Swift_Transport|array Swift transport instance or its array configuration.
     */
    private $_transport = [];

    /**
     *
     * @return array|\Swift_Mailer Swift mailer instance or array configuration.
     */
    public function getSwifSMailer() {
        if (!is_object($this->_swifSMailer)) {
            $this->_swifSMailer = $this->createSwifSMailer();
        }

        return $this->_swifSMailer;
    }

    public function init() {
        parent::init();
        $transport = [
            'class' => 'Swift_SmtpTransport',
            'host' => \Yii::$app->settings->smtp->config->host,
            'username' => \Yii::$app->settings->smtp->config->username,
            'password' => \Yii::$app->settings->smtp->config->password,
            'port' => \Yii::$app->settings->smtp->config->port,
            'encryption' => \Yii::$app->settings->smtp->config->encryption,
            'streamOptions' => [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true
                ]
            ]
        ];
        $this->_transport = $transport;
    }

    /**
     *
     * @return array|\Swift_Transport
     */
    public function getTransport() {
        if (!is_object($this->_transport)) {
            $this->_transport = $this->createTransport($this->_transport);
        }

        return $this->_transport;
    }

    /**
     *
     * @inheritdoc
     */
    protected function sendMessage($message) {
        $address = $message->getTo();
        if (is_array($address)) {
            $address = implode(', ', array_keys($address));
        }
        \Yii::info('Sending email "' . $message->getSubject() . '" to "' . $address . '"', __METHOD__);

        return $this->getSwifSMailer()->send($message->getSwiftMessage()) > 0;
    }

    /**
     * Creates Swift mailer instance.
     *
     * @return \Swift_Mailer mailer instance.
     */
    protected function createSwifSMailer() {
        if (method_exists('Swift_Mailer', 'newInstance')) {
            return \Swift_Mailer::newInstance($this->getTransport());
        }
        return new \Swift_Mailer($this->getTransport());
    }

    /**
     * Creates email transport instance by its array configuration.
     *
     * @param array $config
     *            transport configuration.
     * @throws \yii\base\InvalidConfigException on invalid transport configuration.
     * @return \Swift_Transport transport instance.
     */
    protected function createTransport(array $config) {
        if (!isset($config['class'])) {
            $config['class'] = 'Swift_MailTransport';
        }
        if (isset($config['plugins'])) {
            $plugins = $config['plugins'];
            unset($config['plugins']);
        } else {
            $plugins = [];
        }

        if ($this->enableSwifSMailerLogging) {
            $plugins[] = [
                'class' => 'Swift_Plugins_LoggerPlugin',
                'constructArgs' => [
                    [
                        'class' => 'yii\swifSMailer\Logger'
                    ]
                ]
            ];
        }

        /* @var $transport */
        $transport = $this->createSwiftObject($config);
        if (!empty($plugins)) {
            foreach ($plugins as $plugin) {
                if (is_array($plugin) && isset($plugin['class'])) {
                    $plugin = $this->createSwiftObject($plugin);
                }
                $transport->registerPlugin($plugin);
            }
        }

        return $transport;
    }

    /**
     * Creates Swift library object, from given array configuration.
     *
     * @param array $config
     *            object configuration
     * @return Object created object
     * @throws \yii\base\InvalidConfigException on invalid configuration.
     */
    protected function createSwiftObject(array $config) {
        if (isset($config['class'])) {
            $className = $config['class'];
            unset($config['class']);
        } else {
            throw new InvalidConfigException('Object configuration must be an array containing a "class" element.');
        }

        if (isset($config['constructArgs'])) {
            $args = [];
            foreach ($config['constructArgs'] as $arg) {
                if (is_array($arg) && isset($arg['class'])) {
                    $args[] = $this->createSwiftObject($arg);
                } else {
                    $args[] = $arg;
                }
            }
            unset($config['constructArgs']);
            $object = \Yii::createObject($className, $args);
        } else {
            $object = \Yii::createObject($className);
        }

        if (!empty($config)) {
            $reflection = new \ReflectionObject($object);
            foreach ($config as $name => $value) {
                if ($reflection->hasProperty($name) && $reflection->getProperty($name)->isPublic()) {
                    $object->$name = $value;
                } else {
                    $setter = 'set' . $name;
                    if ($reflection->hasMethod($setter) || $reflection->hasMethod('__call')) {
                        $object->$setter($value);
                    } else {
                        throw new InvalidConfigException('Setting unknown property: ' . $className . '::' . $name);
                    }
                }
            }
        }
        return $object;
    }

}
