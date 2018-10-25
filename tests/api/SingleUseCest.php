<?php

use Codeception\Util\HttpCode;

/**
 * @author Angel (Faryshta) Guevara <aguevara@alquimiadigital.mx>
 */
class SingleUseTokenCest
{
    /**
     * @depends ResourceOwnerPasswordCredentialsCest:fixtures
     */
    public function singleUseRequest(ApiTester $I)
    {
        $I->wantTo('Request a resource and validate the token expires after.');

        $token = ResourceOwnerPasswordCredentialsCest::$token;
        $I->sendGET('/site/single-use', [
            'accessToken' => $token,
        ]);

        $I->seeResponseCodeIs(HttpCode::OK);

        $I->sendGET('/site/single-use', [
            'accessToken' => $token,
        ]);

        $I->seeResponseCodeIs(HttpCode::UNAUTHORIZED); 
    }
}

