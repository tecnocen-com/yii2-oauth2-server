<?php

namespace tecnocen\oauth2server\fixtures;

use tecnocen\oauth2server\models\OauthClients;

/**
 * Fixture to load default clients.
 */
class OauthClientsFixture extends \yii\test\ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = OauthClients::class;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/clients.php';
}
