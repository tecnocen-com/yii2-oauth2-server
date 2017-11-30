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
            if (is_string($this->oauth2Module)) {
                $this->oauth2Module = Yii::$app->getModule(
                    $this->oauth2Module
                );
            }
            $this->oauth2Module->initOauth2Server();
            $this->oauth2Module->getServer()->verifyResourceRequest();

            return true;
        }

        return false;
    }
}
