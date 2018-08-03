<?php

namespace tecnocen\oauth2server\controllers;

use tecnocen\oauth2server\filters\ErrorToExceptionFilter;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\OptionsAction;

/**
 * @property tecnocen\oauth2server\Module $module
 */
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
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'token' => ['POST'],
            'options' => ['OPTIONS'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'options' => [
                'class' => Options::class,
                'collectionOptions' => ['POST', 'OPTIONS'],
                'resourceOptions' => ['OPTIONS'],
            ],
        ];
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
