<?php

/**
*@copyright :Amusoftech Pvt. Ltd. < www.amusoftech.com >
*@author     : Ram mohamad Singh< er.amudeep@gmail.com >
*/


namespace app\models;

use app\models\User;
use Yii;
use yii\base\Model;

/**
 * Password reset request form
 */
class PasswordReseSRequestForm extends Model {
	public $email;
	
	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [ 
				[ 
						'email',
						'filter',
						'filter' => 'trim' 
				],
				[ 
						'email',
						'required' 
				],
				[ 
						'email',
						'email' 
				],
				[ 
						'email',
						'exist',
						'targetClass' => '\app\models\User',
						'filter' => [ 
								'state_id' => User::STATE_ACTIVE 
						],
						'message' => 'There is no user with such email.' 
				] 
		];
	}
	
	/**
	 * Sends an email with a link, for resetting the password.
	 *
	 * @return boolean whether the email was send
	 */
	public function sendEmail() {
		/* @var $user User */
		$user = User::findOne ( [ 
				'state_id' => User::STATE_ACTIVE,
				'email' => $this->email 
		] );
		if (! $user) {
			return false;
		}
		if (! User::isPasswordResetTokenValid ( $user->activation_key )) {
			$user->generatePasswordResetToken ();
		}
		if (! $user->save ()) {
			return false;
		}
		EmailQueue::add ( [ 
				'from' => \Yii::$app->params ['adminEmail'],
				'to' => $this->email,
				'subject' => \Yii::$t ( 'app', 'Password reset for ' . \Yii::$app->name ),
				'view' => 'passwordResetToken',
				'viewArgs' => [ 
						'user' => $user 
				] 
		] );
		return true;
	}
}
