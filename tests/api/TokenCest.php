<?php

use app\fixtures\UserFixture;
use Codeception\Util\HttpCode;
use tecnocen\oauth2server\fixtures\OauthClientsFixture;

class TokenCest
{
    public function fixtures(ApiTester $I)
    {
        $I->haveFixtures([
            'user' => UserFixture::class,
            'clients' => OauthClientsFixture::class,
        ]);
    }

    /**
     * @depends fixtures
     */
    public function create(ApiTester $I)
    {
        $I->wantTo('Create a token using REST service.');
        $I->amHttpAuthenticated('testclient', 'testpass');

        $I->sendPOST('/oauth2/token', [
            'grant_type' => 'password',
            'username' => 'erau',
            'password' => 'password_0',
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);
        $I->seeResponseIsJson();
        $I->seeResponseMatchesJsonType([
            'access_token' => 'string:regex(/[0-9a-f]{40}/)',
            'refresh_token' => 'string:regex(/[0-9a-f]{40}/)',
        ]);
    }
}
