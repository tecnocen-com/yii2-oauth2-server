<?php

namespace tecnocen\oauth2server\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use tecnocen\oauth2server\filters\ErrorToExceptionFilter;

class RestController extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::class,
                'oauth2Module' => $this->module,
            ],
        ]);
    }

    /**
     * Action to generate oauth2 tokens.
     */
    public function actionToken()
    {
        return $this->module->getServer()->handleTokenRequest()
            ->getParameters();
    }
}
