<?php

namespace app\components;

class WebUser extends \yii\web\User {

    public $_modeAdmin = false;
    public $enableAutoLogin = true;
    public $identityClass = 'app\models\User';
    public $loginUrl = [
        '/user/login'
    ];
    public $authTimeout = 86400;

    public function init() {
        parent::init();
        $cookiePath = '/';
        $path = \Yii::$app->request->baseUrl;
        if (!empty($path)) {
            $cookiePath = $path;
        }
        $this->identityCookie['name'] = '_user_' . \Yii::$app->id;
        $this->identityCookie['path'] = $cookiePath;
    }

    public function afterLogin($identity, $cookieBased, $duration) {
        $identity->last_visit_time = date('Y-m-d H:i:s');
        $identity->updateAttributes([
            'last_visit_time'
        ]);
        $this->setIsAdminMode();
        return parent::afterLogin($identity, $cookieBased, $duration);
    }

    public function afterLogout($identity) {
        // $this->cleanupCookies();
    }

    public function getIsAdminMode() {
        $this->_modeAdmin = \Yii::$app->session->get('ADMIN_MODE', false);
        return $this->_modeAdmin;
    }

    public function setIsAdminMode($mode = false) {
        $this->_modeAdmin = $mode;
        \Yii::$app->session->set('ADMIN_MODE', $mode);
    }

    public function cleanupCookies() {
        $past = time() - 3600;
        foreach ($_COOKIE as $key => $value) {
            setcookie($key, false, $past, '/');
        }
    }

    public function can($permissionName, $params = [], $allowCaching = true) {
        return parent::can($permissionName, $params, $allowCaching);
    }

    public function canRoute($module, $route = null, $allowCaching = true, $defaultValue) {
        if (($accessChecker = $this->getAuthAccessChecker()) === false) {
            return $defaultValue;
        }

        return $accessChecker->canRoute($module, $route, $allowCaching, $defaultValue);
    }

    public function getAuthAccessChecker() {
        if (($accessChecker = $this->getAccessChecker()) === null) {
            return false;
        }

        return $accessChecker;
    }

}
