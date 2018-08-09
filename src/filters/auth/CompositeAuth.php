<?php

namespace tecnocen\oauth2server\filters\auth;

use tecnocen\oauth2server\filters\ErrorToExceptionTrait;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\helpers\StringHelper;
use yii\web\HttpException;

class CompositeAuth extends \yii\filters\auth\CompositeAuth
{
    use ErrorToExceptionTrait {
        ErrorToExceptionTrait::beforeAction as traitBeforeAction;
    }

    /**
     * @var string[] pairs of $actionPattern => $scope to require an scope for
     * specific actions comparing them with their action id. Wildcards like '*'
     * are allowed.
     *
     * If several $actionPatterns match the action being processed only the
     * first one will be used.
     *
     * @see https://www.yiiframework.com/doc/api/2.0/yii-helpers-basestringhelper#matchWildcard()-detail
     */
    public $actionScopes = [];

    /**
     * @inheritdoc
     */
    public $authMethods = [
        ['class' => HttpBearerAuth::class],
        [
            'class' => QueryParamAuth::class,
            'tokenParam' => 'accessToken',
        ],
    ];

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        if ($this->traitBeforeAction($action)) {
            $this->oauth2Module->getServer()->verifyResourceRequest(
                null,
                null,
                $this->fetchActionScope($action->getUniqueId())
            );
            $this->ensureSuccessResponse();

            return true;
        }

        return false;
    }

    /**
     * Fetch the scope required for the action id.
     *
     * @param string $actionId
     * @return ?string the required scope or `null` if no scope is required.
     */
    protected function fetchActionScope($actionId)
    {
        if (empty($this->actionScopes)) {
            return null;
        }

        $ownerId = $this->owner->getUniqueId();
        foreach ($this->actionScopes as $actionPattern => $scope) {

            if (StringHelper::matchWildcard(
                "$ownerId/$actionPattern",
                $actionId
            )) {
                return $scope;
            }
        }

        return null;
    }
}
