Yii2 OAuth2 Server Library
==========================

4.1.0
-----

- [Enh] Functionality to revoke a token after it gets used by
  `tecnocen\oauth2server\filters\RevokeAccessToken` and
  `tecnocen\oauth2server\RevokeAccessTokenInterface`

4.0.1
-----

- [Bug] Add action for OPTIONS verb on
  `tecnocen\oauth2server\controllers\RestController` (Faryshta)


4.0.0
-----

- [Brk] `tecnocen\oauth2Server\filters\auth\CompositeAuth` checks for errors on
  the oauth2 request simplifying the use to just one behavior
- [Enh] Live Demo on `tests/_app` initialized by command
  `tests/_app/yii.php serve`
- [Brk] Support for `OPTIONS` requests needed on CORS.
- [Enh] `tecnocen\oauth2Server\filters\auth\CompositeAuth::$actionScopes`
  allows to set which scopes a token must have for determined actions.


3.0.2
-----

- [Bug] `tecnocen\oauth2Server\controllers\RestController::behaviors()`
  obtains the `oauth2Module` from parent module.


3.0.1
-----

- [Bug] Initialize oauth2Module on `EVENT_BEFORE_ACTION`.

3.0.0
-----

- [BRK] Taking over the project changing the namespaces.
- [Enh] Create codeception api tests.
- [Enh] Initialize oauth2 translations using `yii\base\Application::$bootstrap`
- [Enh] Initialize oauth2 server using `yii\base\Module::EVENT_BEFORE_ACTION`
- [Enh] Update migrations
- [Enh] Support for yii2 version 2.0.13
