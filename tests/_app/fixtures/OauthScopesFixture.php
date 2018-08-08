<?php

namespace app\fixtures;

use tecnocen\oauth2server\models\OauthScopes;

class OauthScopesFixture extends \yii\test\ActiveFixture
{
    public $modelClass = OauthScopes::class;
    public $dataFile = __DIR__ . '/data/scopes.php';
}
