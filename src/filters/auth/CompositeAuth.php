<?php

namespace tecnocen\oauth2server\filters\auth;

use Yii;

class CompositeAuth extends \yii\filters\auth\CompositeAuth
{
    /**
     * @var string the unique id for the oauth2 module 
     */
    public $oauth2Module = 'oauth2';

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        
        if (parent::beforeAction($action)) {
            $this->oauth2Module->initOauth2Server();
            $this->oauth2Module->getServer()->verifyResourceRequest();

            return true;
        }

        return false;
    }
}
