yii2-oauth2-server
==================

A wrapper for implementing an
[OAuth2 Server](https://github.com/bshaffer/oauth2-server-php).

This project was forked from
[Filsh Original Project](https://github.com/Filsh/yii2-oauth2-server) but the
changes are not transparent, read [UPGRADE.md] to pass to the latest version.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist tecnocen/yii2-oauth2-server "*"
```

or add

```json
"tecnocen/yii2-oauth2-server": "~2.1"
```

to the require section of your composer.json.

Usage
-----

To use this extension,  simply add the following code in your application configuration as a new module:

```php
    'bootstrap' => ['oauth2'],
    'modules'=>[
        //other modules .....
        'oauth2' => [
            'class' => 'tecnocen\oauth2server\Module',            
            'tokenParamName' => 'accessToken',
            'tokenAccessLifetime' => 3600 * 24,
            'storageMap' => [
                'user_credentials' => 'app\models\User',
            ],
            'grantTypes' => [
                'user_credentials' => [
                    'class' => 'OAuth2\GrantType\UserCredentials',
                ],
                'refresh_token' => [
                    'class' => 'OAuth2\GrantType\RefreshToken',
                    'always_issue_new_refresh_token' => true
                ]
            ]
        ]
    ],
```

Bootstrap will initialize translation and add the required url rules to
`Yii::$app->urlManager`.

### JWT tokens

There is no JWT token support on this fork, feel free to submit a
(pull request)[https://github.com/tecnocen-com/yii2-oauth2-server/pulls] to
enable this functionality.

### UserCredentialsInterface

The class passed to `Yii::$app->user->identityClass` must implement the interface
`\OAuth2\Storage\UserCredentialsInterface`, to store oauth2 credentials in user
table.

```php
use Yii;

class User extends common\models\User
    implements \OAuth2\Storage\UserCredentialsInterface
{

    /**
     * Implemented for Oauth2 Interface
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var \tecnocen\oauth2server\Module $module */
        $module = Yii::$app->getModule('oauth2');
        $token = $module->getServer()->getResourceController()->getToken();
        return !empty($token['user_id'])
                    ? static::findIdentity($token['user_id'])
                    : null;
    }

    /**
     * Implemented for Oauth2 Interface
     */
    public function checkUserCredentials($username, $password)
    {
        $user = static::findByUsername($username);
        if (empty($user)) {
            return false;
        }
        return $user->validatePassword($password);
    }

    /**
     * Implemented for Oauth2 Interface
     */
    public function getUserDetails($username)
    {
        $user = static::findByUsername($username);
        return ['user_id' => $user->getId()];
    }
}
```

### Migrations

The next step your shold run migration

```php
yii migrate all -p=@tecnocen/oauth2server/migrations/tables
yii fixture "*" -n=tecnocen/oauth2server/fixtures
```

this migration create the oauth2 database scheme. The second command insert
test user credentials ```testclient:testpass``` for ```http://fake/```

### Controllers

To support authentication by access token. Simply add the behaviors for your controller or module.

```php
use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use tecnocen\oauth2server\filters\auth\CompositeAuth;

class Controller extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => [
                'class' => CompositeAuth::class,
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    [
                        'class' => QueryParamAuth::class,
                        'tokenParam' => 'accessToken',
                    ],
                ]
            ],
        ]);
    }
}
```

The code above is the same as the default implementation which can be
simplified as

```php
use yii\helpers\ArrayHelper;
use tecnocen\oauth2server\filters\auth\CompositeAuth;

class Controller extends \yii\rest\Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'authenticator' => CompositeAuth::class,
        ]);
    }
}
```

### Scopes

The property `tecnocen\oauth2server\filters\auth\CompositeAuth::$actionScopes`
set which actions require specific scopes. If those scopes are not meet the
action wont be executed, and the server will reply with an HTTP Status Code 403.

```php
public function behaviors()
{
    return ArrayHelper::merge(parent::behaviors(), [
        'authenticator' => [
            'class' => CompositeAuth::class,
            'actionScopes' => [
                'create' => 'default create',
                'update' => 'default edit',
                '*' => 'default', // wildcards are allowed
            ]
        ],,
    ]);
}
```

### Automatically Revoke Tokens

Sometimes its neccessary to revoke a token on each request to prevent the
request from being triggered twice.

Attaching the action filter `tecnocen\oauth2server\filters\RevokeAccessToken`
allows to configure the actions to automatically revoke the access token.

```php
public function behaviors()
{
    return [
        'revokeToken' => [
            'class' => \tecnocen\oauth2server\filters\RevokeAccessToken::class,
            // optional only revoke the token if it has any of the following
            // scopes. if not defined it will always revoke the token.
            'scopes' => ['author', 'seller'],
            // optional whether or not revoke all tokens or just the active one
            'revokeAll' => true,
            // optional if non authenticated users are permited.
            'allowGuests' => true,
            // which actions this behavior applies to.
            'only' => ['create', 'update'],
        ]
    ];
}
```

### Generate Token with JS

To get access token (js example):

```js
var url = window.location.host + "/oauth2/token";
var data = {
    'grant_type':'password',
    'username':'<some login from your user table>',
    'password':'<real pass>',
    'client_id':'testclient',
    'client_secret':'testpass'
};
//ajax POST `data` to `url` here
//
```

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and the process for submitting pull requests to us.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details


For more, see https://github.com/bshaffer/oauth2-server-php
