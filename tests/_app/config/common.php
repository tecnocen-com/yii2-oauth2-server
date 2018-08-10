<?php

return [
    'basePath' => dirname(__DIR__),
    'language' => 'en-US',
    'aliases' => [
        '@tests' => dirname(dirname(__DIR__)),
        '@tecnocen/oauth2server' => dirname(dirname(dirname(__DIR__))) . '/src',
    ],
    'components' => [
        'db' => require __DIR__ . '/db.php',
        'authManager' => [
             'class' => yii\rbac\DbManager::class,
        ],
    ],
];
