<?php

namespace tecnocen\oauth2server\filters;

use Yii;
use yii\base\Controller;
use tecnocen\oauth2server\Module;
use tecnocen\oauth2server\exceptions\HttpException;

class ErrorToExceptionFilter extends \yii\base\Behavior
{
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
     * @throws HttpException when the request method is not allowed.
     */
    public function afterAction($event)
    {
        $response = $this->owner->module->getServer()->getResponse();

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
    }
    
    protected function getErrorMessage(\OAuth2\Response $response)
    {
        $message = Module::t('common', $response->getParameter('error_description'));
        if($message === null) {
            $message = Module::t('common', 'An internal server error occurred.');
        }
        return $message;
    }
}
