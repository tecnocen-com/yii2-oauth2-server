<?php

namespace tecnocen\oauth2server\filters\auth;

use tecnocen\oauth2server\filters\ErrorToExceptionTrait;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\web\HttpException;

class CompositeAuth extends \yii\filters\auth\CompositeAuth
{
    use ErrorToExceptionTrait {
        ErrorToExceptionTrait::beforeAction as traitBeforeAction;
    }

    /**
     * @inheritdoc
     */
    public $authMethods = [
        ['class' => HttpBearerAuth::class],
        [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'accessToken',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($this->traitBeforeAction($action)) {
            $this->oauth2Module->getServer()->verifyResourceRequest();

            return true;
        }

        return false;
    }
}
