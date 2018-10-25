<?php

namespace tecnocen\oauth2server;

use tecnocen\oauth2server\models\OauthAccessTokens as AccessToken;

trait RevokeAccessTokenTrait
{
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->innerJoinWith([
            'activeAccessToken' => function ($query) use ($token) {
                $query->andWhere(['access_token' => $token]);
            },
        ])->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAccessTokens()
    {
        return $this->hasMany(AccessToken::class, ['user_id' => 'id']);
    }

    public function getActiveAccessToken()
    {
        $query = $this->getAccessTokens();
        $query->multiple = false;

        return $query;
    }

    public function getAccessTokenData()
    {
        return $this->activeAccessToken;
    }

    public function revokeActiveAccessToken()
    {
        return $this->getAccessTokenData()->delete();
    }

    public function revokeAllAccessTokens()
    {
        return AccessToken::deleteAll(['user_id' => $this->id]) > 0;
    }

}
