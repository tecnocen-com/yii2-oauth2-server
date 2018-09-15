<?php

namespace tecnocen\oauth2server;

use tecnocen\oauth2server\models\OauthAccessTokens as AccessToken;
use yii\web\IdentityInterface;

/**
 * Enables the functionality for the authenticated user to revoke all access
 * tokens or just the active one.
 */
interface RevokeAccessTokenInterface extends IdentityInterface
{
    /**
     * @return AccessToken
     */
    public function getAccessTokenData();

    /**
     * Revokes the active access token used for the authentication.
     *
     * @return bool if the operaction was successful
     */
    public function revokeActiveAccessToken();

    /**
     * Revokes all access tokens for the authenticated user.
     *
     * @return bool if the operaction was successful
     */
    public function revokeAllAccessTokens();
}
