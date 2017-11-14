<?php

return [
    'id' => 'yii2-oauth2-server-tests',
    'basePath' => dirname(__DIR__),
    'language' => 'en-US',
    'aliases' => [
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
        '@bower' => VENDOR_DIR . '/bower',
    ],
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
        'db' => require __DIR__ . '/db.php',
        'mailer' => [
            'useFileTransport' => true,
        ],
        'user' => ['identityClass' => app\models\User::class],
        'urlManager' => [
            'showScriptName' => true,
            'enablePrettyUrl' => true,
            'rules' => ['POST oauth2/<action:\w+>' => 'oauth2/rest/<action>'],
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ],
    ],
    'params' => [],
];
