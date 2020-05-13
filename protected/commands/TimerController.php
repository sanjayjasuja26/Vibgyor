<?php

/**
*@author     : Sanjay Jasuja< sanjayjasuja26@gmail.com >
*/
namespace app\commands;

use app\components\TConsoleController;
use app\models\EmailQueue;

class TimerController extends TConsoleController
{

    const MAX_ATTEMPTS = 5;

    public function actionEmail()
    {
        $query = EmailQueue::find()->where([
            'state_id' => EmailQueue::STATE_PENDING
        ])->orderBy('id asc');

        foreach ($query->batch(50) as $mails) {
            foreach ($mails as $mail) {
                $mail->sendNow();
            }
        }

        return true;
    }


}


