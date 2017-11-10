<?php

namespace tecnocen\oauth2server;

use OAuth2\ClientAssertionType\ClientAssertionTypeInterface;
use OAuth2\RequestInterface;
use OAuth2\ResponseInterface;
use OAuth2\ScopeInterface;
use OAuth2\TokenType\TokenTypeInterface;

class Server extends \OAuth2\Server
{
    /**
     * @var Module
     */
    protected $module;

    public function __construct(
        Module $module,
        $storage = [],
        array $config = [],
        array $grantTypes = [],
        array $responseTypes = [],
        TokenTypeInterface $tokenType = null,
        ScopeInterface $scopeUtil = null,
        ClientAssertionTypeInterface $clientAssertionType = null
    ) {
        $this->module = $module;
        parent::__construct(
            $storage,
            $config,
            $grantTypes,
            $responseTypes,
            $tokenType,
            $scopeUtil,
            $clientAssertionType
        );
    }

    public function createAccessToken(
        $clientId,
        $userId,
        $scope = null,
        $includeRefreshToken = true
    ) {
        return $this->getAccessTokenResponseType()->createAccessToken(
            $clientId,
            $userId,
            $scope,
            $includeRefreshToken
        );
    }

    /**
     * @inheritdoc
     */
    public function verifyResourceRequest(
        RequestInterface $request = null,
        ResponseInterface $response = null,
        $scope = null
    ) {
        parent::verifyResourceRequest(
            $request ?: $this->module->getRequest(),
            $response,
            $scope
        );
    }

    /**
     * @inheritdoc
     */
    public function handleTokenRequest(
        RequestInterface $request = null,
        ResponseInterface $response = null
    ) {
        return parent::handleTokenRequest(
            $request ?: $this->module->getRequest(),
            $response
        );
    }
}
