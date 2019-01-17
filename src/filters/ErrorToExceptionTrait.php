<?php

namespace tecnocen\oauth2server\filters;

use tecnocen\oauth2server\Module;
use tecnocen\oauth2server\exceptions\HttpTokenException;
use Yii;
use yii\web\HttpException;

/**
 *
 */
trait ErrorToExceptionTrait
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

            return true;
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function afterAction($event, $result)
    {
        $this->ensureSuccessResponse();

        return $result;
    }

    protected function ensureSuccessResponse()
    {
        $response = $this->oauth2Module->getResponse();
        if($response === null
            || $response->isInformational()
            || $response->isSuccessful()
            || $response->isRedirection()
        ) {
            return;
        }

        throw new HttpTokenException(
            $response->getStatusCode(),
            $this->getErrorMessage($response),
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
                'An internal server error occurred'
            );
    }
}
