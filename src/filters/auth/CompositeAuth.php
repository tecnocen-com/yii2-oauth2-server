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
    
    

    /**
     * @inheritdoc
     */
    public function afterAction($event, $result)
    {
        $response = $this->oauth2Module->getServer()->getResponse();

        if($response === null
            || $response->isInformational()
            || $response->isSuccessful()
            || $response->isRedirection()
        ) {
            return;
        }

        throw new HttpException(
            $response->getStatusCode(),
            $this->getErrorMessage($response),
            $response->getParameter('error_uri')
        );

        return $result;
    }

    /**
     * @return string the error message shown on the response.
     */
    protected function getErrorMessage(\OAuth2\Response $response)
    {
        return Module::t(
                'oauth2server',
                $response->getParameter('error_description')
            )
            ?: Module::t(
                'oauth2server',
                'An internal server error occurred.'
            );
    }
}
