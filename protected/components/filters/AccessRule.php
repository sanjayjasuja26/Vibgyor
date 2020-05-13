<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
 */
namespace app\components\filters;

use yii\base\InvalidConfigException;
use yii\web\Request;
use yii\base\Action;
use app\components\WebUser;

class AccessRule extends \yii\filters\AccessRule
{

    public $ranks;

    public $groups;

    /**
     * Checks whether the Web user is allowed to perform the specified action.
     *
     * @param Action $action
     *            the action to be performed
     * @param WebUser|false $user
     *            the user object or `false` in case of detached User component
     * @param Request $request
     * @return bool|null `true` if the user is allowed, `false` if the user is denied, `null` if the rule does not apply to the user
     */
    public function allows($action, $user, $request)
    {
        if ($this->matchAction($action) 
            && $this->matchGroup($user) 
            && $this->matchRoleRank($user) 
            && $this->matchRole($user) 
            && $this->matchIP($request->getUserIP()) 
            && $this->matchVerb($request->getMethod()) 
            && $this->matchController($action->controller) 
            && $this->matchCustom($action)) {
            return $this->allow ? true : false;
        }

        return null;
    }

    /**
     *
     * @param WebUser $user
     *            the user object
     * @return bool whether the rule applies to the role
     * @throws InvalidConfigException if User component is detached
     */
    protected function matchRole($user)
    {
        $items = empty($this->roles) ? [] : $this->roles;

        if (! empty($this->permissions)) {
            $items = array_merge($items, $this->permissions);
        }

        if (empty($items)) {
            return true;
        }

        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify roles in AccessRule.');
        }

        foreach ($items as $item) {
            if ($item === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($item === '@') {
                if (! $user->getIsGuest()) {
                    return true;
                }
            } else {

                $accessChecker = $user->getAuthAccessChecker();
                
                foreach ($items as $item) {
                    $access = $accessChecker->checkAccessRole($user->getId(), $item);
                    if ($access) {
                        return true;
                    }
                }
              
            }
        }

        return false;
    }

    /**
     *
     * @param WebUser $user
     *            the user object
     * @return bool whether the rule applies to the role
     * @throws InvalidConfigException if User component is detached
     */
    protected function matchRoleRank($user)
    {
        $items = empty($this->ranks) ? [] : $this->ranks;

        if (empty($items)) {
            return true;
        }
           
        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify roles in AccessRule.');
        }
        if ($user->getIsGuest()) {
            return false;
        }

        $accessChecker = $user->getAuthAccessChecker();
            
        foreach ($items as $item) {
            $access = $accessChecker->checkAccessRoleRank($user->getId(), $item);
            if ($access) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @param WebUser $user
     *            the user object
     * @return bool whether the rule applies to the role
     * @throws InvalidConfigException if User component is detached
     */
    protected function matchGroup($user)
    {
        $items = empty($this->groups) ? [] : $this->groups;

        if (empty($items)) {
            return true;
        }

        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify groups in AccessRule.');
        }
        if ($user->getIsGuest()) {
            return false;
        }

        $accessChecker = $user->getAuthAccessChecker();

        foreach ($items as $item) {
            //echo $item;exit;
            $access = $accessChecker->checkAccessGroup($user->getId(), $item);
            if ($access) {
                return true;
            }
        }

        return false;
    }
}