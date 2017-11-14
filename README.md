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
If you want to get Json Web Token (JWT) instead of convetional token, you will need to set `'useJwtToken' => true` in module and then define two more configurations: 
`'public_key' => 'app\storage\PublicKeyStorage'` which is the class that implements [PublickKeyInterface](https://github.com/bshaffer/oauth2-server-php/blob/develop/src/OAuth2/Storage/PublicKeyInterface.php) and `'access_token' => 'app\storage\JwtAccessToken'` which implements [JwtAccessTokenInterface.php](https://github.com/bshaffer/oauth2-server-php/blob/develop/src/OAuth2/Storage/JwtAccessTokenInterface.php)

For Oauth2 base library provides the default [access_token](https://github.com/bshaffer/oauth2-server-php/blob/develop/src/OAuth2/Storage/JwtAccessToken.php) which works great except that it tries to save the token in the database. So I decided to inherit from it and override the part that tries to save (token size is too big and crashes with VARCHAR(40) in the database.

TL;DR, here are the sample classes
**access_token**
```php
<?php

namespace app\storage;

/**
 *
 * @author Stefano Mtangoo <mwinjilisti at gmail dot com>
 */
class JwtAccessToken extends \OAuth2\Storage\JwtAccessToken
{  
    public function setAccessToken($oauth_token, $client_id, $user_id, $expires, $scope = null)
    {
         
    }

    public function unsetAccessToken($access_token)
    {
        
    } 
}

```

and **public_key**

```php
<?php
namespace app\storage;

class PublicKeyStorage implements \OAuth2\Storage\PublicKeyInterface{


    private $pbk =  null;
    private $pvk =  null; 
    
    public function __construct()
    {
        //files should be in same directory as this file
        //keys can be generated using OpenSSL tool with command: 
        /*
          private key:
          openssl genrsa -out privkey.pem 2048

          public key:
          openssl rsa -in privkey.pem -pubout -out pubkey.pem
        */
        $this->pbk =  file_get_contents('privkey.pem', true); 
        $this->pvk =  file_get_contents('pubkey.pem', true); 
    }

    public function getPublicKey($client_id = null){ 
        return  $this->pbk;
    }

    public function getPrivateKey($client_id = null){ 
        return  $this->pvk;
    }

    public function getEncryptionAlgorithm($client_id = null){
        return 'HS256';
    }

}

```
**NOTE:** You will need [this](https://github.com/bshaffer/oauth2-server-php/pull/690) PR applied or you can patch it yourself by checking changes in [this diff](https://github.com/hosannahighertech/oauth2-server-php/commit/ec79732663547065c041e279109137a423eac0cb). The other part of PR is only if you want to use firebase JWT library (which is not mandatory anyway).

<<<<<<< HEAD
Also, extend ```common\models\User``` - user model - implementing the interface ```\OAuth2\Storage\UserCredentialsInterface```, so the oauth2 credentials data stored in user table.
You should implement:
- findIdentityByAccessToken()
- checkUserCredentials()
- getUserDetails()

You can extend the model if you prefer it (please, remember to update the config files) :
```
use Yii;

class User extends common\models\User implements \OAuth2\Storage\UserCredentialsInterface
=======
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
>>>>>>> fork
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
yii migrate --migrationPath=@tecnocen/oauth2server/migrations/tables
yii fixture all --migrationPath=@tecnocen/oauth2server/fixtures
```

this migration create the oauth2 database scheme. The second command insert
test user credentials ```testclient:testpass``` for ```http://fake/```


### urlManager

```php
'urlManager' => [
    'enablePrettyUrl' => true, //only if you want to use petty URLs
    'rules' => [
        'POST oauth2/<action:\w+>' => 'oauth2/rest/<action>',
        ...
    ]
]
```

### Controllers

To use this extension,  simply add the behaviors for your base controller:

```php
use yii\helpers\ArrayHelper;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use tecnocen\oauth2server\filters\ErrorToExceptionFilter;
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
                    ['class' => QueryParamAuth::class, 'tokenParam' => 'accessToken'],
                ]
            ],
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::class
            ],
        ]);
    }
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

For more, see https://github.com/bshaffer/oauth2-server-php
