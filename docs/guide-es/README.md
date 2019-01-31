Yii2 OAuth2 Server
==================

Una envoltura para implementar
[OAuth2 Server](https://github.com/bshaffer/oauth2-server-php).

Este proyecto fue bifurcado de
[Proyecto Original de Filsh](https://github.com/Filsh/yii2-oauth2-server)
sin embargo los cambios no son transparentes, se recomienda leer
[Guia de Actualizacion](UPGRADE.md) para usar la versión mas reciente.

Instalación
-----------

La manera preferida de instalar esta extensión es mediante [composer](http://getcomposer.org/download/).

Ya sea corriendo


```
php composer.phar require --prefer-dist tecnocen/yii2-oauth2-server "*"
```

o agregando

```json
"tecnocen/yii2-oauth2-server": "~4.1"
```

a la sección `require` de tu archivo `composer.json`.

Uso
---

Para usar esta extensión simplemente agrega el siguiente código en tu
configuración de aplicación como un nuevo módulo

```php
    'bootstrap' => ['oauth2'],
    'modules'=>[
        // otros modulos ...
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

El módulo será automáticamente inicializado con las traducciones y agregará
las reglas de url necesarias a `Yii::$app->urlManager`.

### Tokens JWT

No existe soporte para tokens JWT en esta bifurcación, sientanse libres de
enviar (pull request)[https://github.com/tecnocen-com/yii2-oauth2-server/pulls]
para habilitar esta funcionalidad.

### UserCredentialsInterface

La clase proporcionada en `Yii::$app->user->identityClass` debe implementar la
interface `OAuth2\Storage\UserCredentialsInterface` para poder almacenar
credenciales de oauth2.

```php
use Yii;

class User extends common\models\User implements 
    \OAuth2\Storage\UserCredentialsInterface
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

### Migraciones

El siguiente paso es correr las migraciones

```php
yii migrate all -p=@tecnocen/oauth2server/migrations/tables
yii fixture "*" -n=tecnocen/oauth2server/fixtures
```

El primer comando crea el esquema de base de datos de OAuth2. El segundo comando
inserta credenciales de cliente de prueba `testclient:testpass` para
`http://fake/`.

### Controladores

Para soportar la autenticación por token de acceso simplemente agrega los
comportamientos a tu controlador o módulo.

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

El código de arriba es el mismo que la implementación por defecto que puede ser
simplificado como:

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

### Alcances

La propiedad `tecnocen\oauth2server\filters\auth\CompositeAuth::$actionScopes`
determina que acciones requieren alcances (scope) específicos. Si esos alcances
no son cubiertos la acción no sera ejecutado, y el servidor responderá con un
Código de Estado HTTP 403.

```php
public function behaviors()
{
    return ArrayHelper::merge(parent::behaviors(), [
        'authenticator' => [
            'class' => CompositeAuth::class,
            'actionScopes' => [
                'create' => 'default create',
                'update' => 'default edit',
                '*' => 'default', // comodines son permitidos
            ]
        ],,
    ]);
}
```

### Automáticamente Revocar Tokens

Algunas veces es necesario revocar el token con cada petición para impedir que
la misma petición sea disparada dos veces.

Para habilitar esta funcionalidad es necesario implementar
`tecnocen\oauth2server\RevokeAccessTokenInterface` en la clase utilizada para
identificar el usuario autenticado.

```php
use OAuth2\Storage\UserCredentialsInterface;
use tecnocen\oauth2server\RevokeAccessTokenInterface;
use tecnocen\oauth2server\RevokeAccessTokenTrait;

class User extend \yii\db\ActiveRecord implement
    UserCredentialsInterface,
    RevokeAccessTokenInterface
{
    use RevokeAccessTokenTrait; // opcional, implementacion por defecto.
    
    // resto de la clase.
}
```

Después usar la clase anterior como configuración para
`Yii::$app->user->identityClass`.

Agregar el filtro de acción `tecnocen\oauth2server\filters\RevokeAccessToken`
el cual permite configurar las acciones para revocar automáticamente el token de acceso.

```php
public function behaviors()
{
    return [
        'revokeToken' => [
            'class' => \tecnocen\oauth2server\filters\RevokeAccessToken::class,
            // opcional solo revocar si el token tiene cualquiera de los
            // siguientes alcances. Si no esta definido siempre revocara el
            // token.
            'scopes' => ['author', 'seller'],
            // opcional si revocar todos los tokens o solo el activo.
            'revokeAll' => true,
            // opcional si usuarios no autenticados son permitidos.
            'allowGuests' => true,
            // que acciones se aplica este filtro.
            'only' => ['create', 'update'],
        ]
    ];
}
```

### Generar Tokens com JS

Para obtener tokens de acceso (ejemplo de js):

```js
var url = window.location.host + "/oauth2/token";
var data = {
    'grant_type':'password',
    'username':'<registro de tu tabla de usuario>',
    'password':'<clave de acceso>',
    'client_id':'testclient',
    'client_secret':'testpass'
};
//ajax POST `data` to `url` here
//
```

## Construido Con

* Yii 2: El Rápido, Seguro y Profesional PHP Framework [http://www.yiiframework.com](http://www.yiiframework.com)

## Código de Conducta

Por favor leer
[CODE_OF_CONDUCT.md](https://github.com/tecnocen-com/yii2-oauth2-server/blob/master/CODE_OF_CONDUCT.md)
para detalles en el código de conducta.

## Contribuir

Por favor lean
[CONTRIBUTING.md](https://github.com/tecnocen-com/yii2-oauth2-server/blob/master/CONTRIBUTING.md)
para detalles en el proceso de enviar pull requests a nosotros.

## Versionamiento

Usamos [SemVer](http://semver.org/) para versionamiento. Para las versiones
disponibles, ver los
[tags en este repositorio](https://github.com/tecnocen-com/yii2-oauth2-server/tags).

_Considerando [SemVer](http://semver.org/) reglas para versionamiento 9, 10 y 11\hablan sobre pre lanzamientos, los cuales no seran usados en Tecnocen-com._

## Autores

* [**Angel Guevara**](https://github.com/Faryshta) -
  *Mantemimiento, refactorizacion* -
  [Tecnocen.com](https://github.com/Tecnocen-com)
* [**Carlos Llamosas**](https://github.com/neverabe) -
  *Revision* - [Tecnocen.com](https://github.com/Tecnocen-com)

Ver tambien lista completa de
[contribuidores](https://github.com/tecnocen-com/yii2-oauth2-server/graphs/contributors)
quienes participaron en este proyecto.

## Licencia

Este proyecto esta licenciado bajo MIT License - ver archivo
[LICENSE.md](LICENSE.md) para detalles.

[![yii2-oauth2-server](https://img.shields.io/badge/Powered__by-Tecnocen.com-orange.svg?style=for-the-badge)](https://www.tecnocen.com/)

Por mas informacion, ver https://github.com/bshaffer/oauth2-server-php
