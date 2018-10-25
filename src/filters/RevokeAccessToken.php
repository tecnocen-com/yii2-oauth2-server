<?php

namespace tecnocen\oauth2server\filters;

use tecnocen\oauth2server\RevokeAccessTokenInterface;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\ForbiddenHttpException;

/**
 * Revokes access tokens before executing an action.
 *
 * > Note: this is called on the before action event to make sure the token is
 * > always revoked even if there was an error in the request, for that reason
 * > its mandatory to have the authentication logic before this behavior is
 * > attached.
 *
 * Usage:
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'authenticator' => [
 *              // logic to auth the user with the token.
 *         ],
 *         'revokeToken' => [
 *              'classs' => RevokeAccessToken::class,
 *              // other options.
 *         ],
 *     ];
 * }
 * ```
 *
 * It is also possible to attach this behavior to a controller with one of its
 * parent modules handling the authentication logic.
 *
 * For this behavior to work the class configured in
 * `Yii::$app->user->$identityClass` must implement
 * `RevokeAccessTokenInterface`.
 */
class RevokeAccessToken extends \yii\base\ActionFilter
{
    /**
     * @var string[] allows you to define scopes that when found revoke the
     * access token. When empty it revokes the access token regardless of scope.
     */
    public $revokableScopes = [];

    /**
     * @var bool if all access token must be revoked or just the active one.
     */
    public $revokeAll = false;

    /**
     * @var bool whether or not allow guest users from accessing the action.
     */
    public $allowGuests = false;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if (Yii::$app->user->getIsGuest()) {
            if ($this->allowGuests) {
                return true;
            } else {
                throw new ForbiddenHttpException(
                    'User must be authenticated for this request.'
                );
            }
        }

        $user = Yii::$app->user->getIdentity();
        if (!$user instanceof RevokeAccessTokenInterface) {
            throw new InvalidConfigException(
                get_class($user) . ' must implement '
                    . RevokeAccessTokenInterface::class
            );
        }

        if (empty($this->revokableScopes)
            || preg_match(
                '/\b(' . implode('|', $this->revokableScopes) . ')\b/',
                $user->getAccessTokenData()->scope
            )
        ) {
            return $this->revokeAll
                ? $user->revokeAllAccessTokens()
                : $user->revokeActiveAccessToken();
        }

        return true;
    }
}
