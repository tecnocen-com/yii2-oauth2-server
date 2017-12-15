<?php

namespace app\controllers;

use yii\web\Controller;

class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            [
                'class' => \tecnocen\oauth2server\filters\auth\CompositeAuth::class,
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
