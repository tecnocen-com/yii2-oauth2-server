<?php

namespace tecnocen\oauth2server\filters;

use Yii;
use yii\base\Controller;
use tecnocen\oauth2server\Module;
use tecnocen\oauth2server\exceptions\HttpTokenException;

/**
 * @deprecated functionality included on auth\CompositeAuth
 */
class ErrorToExceptionFilter extends \yii\base\Behavior
{
    /**
     * @var string the unique id for the oauth2 module 
     */
    public $oauth2Module = 'oauth2';

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [Controller::EVENT_AFTER_ACTION => 'afterAction'];
    }

    /**
     * @param ActionEvent $event
     * @return boolean
     * @throws HttpTokenException when the request method is not allowed.
     */
    public function afterAction($event)
    {
        if (is_string($this->oauth2Module)) {
            $this->oauth2Module = Yii::$app->getModule(
                $this->oauth2Module
            );
        }
        $response = $this->oauth2Module->getServer()->getResponse();

        if($response === null
            || $response->isInformational()
            || $response->isSuccessful()
            || $response->isRedirection()
        ) {
            return;
        }

        throw new HttpTokenException(
            $response->getStatusCode(),
            $response->getResponseBody(),
            $response->getParameter('error_uri')
        );
    }

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
