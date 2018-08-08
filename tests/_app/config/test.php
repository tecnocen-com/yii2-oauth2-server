<?php

return yii\helpers\ArrayHelper::merge(
    require __DIR__ . '/common.php',
        [
        'id' => 'yii2-oauth2-server-tests',
        'bootstrap' => ['oauth2'],
        'modules' => [
            'oauth2' => [
                'class' => tecnocen\oauth2server\Module::class,
                'tokenParamName' => 'accessToken',
                'tokenAccessLifetime' => 3600 * 24,
                'storageMap' => [
                    'user_credentials' => app\models\User::class,
                ],
                'grantTypes' => [
                    'client_credentials' => [
                        'class' => OAuth2\GrantType\ClientCredentials::class,
                    ],
                    'user_credentials' => [
                        'class' => OAuth2\GrantType\UserCredentials::class,
                    ],
                    'refresh_token' => [
                        'class' => OAuth2\GrantType\RefreshToken::class,
                        'always_issue_new_refresh_token' => true
                    ],
                ],
            ],
        ],
        'components' => [
            'mailer' => [
                'useFileTransport' => true,
            ],
            'user' => ['identityClass' => app\models\User::class],
            'urlManager' => [
                'showScriptName' => true,
                'enablePrettyUrl' => true,
            ],
            'request' => [
                'cookieValidationKey' => 'test',
                'enableCsrfValidation' => false,
            ],
            'errorHandler' => [
                'class' => app\components\ErrorHandler::class,
            ],
        ],
        'params' => [],
    ]
);
