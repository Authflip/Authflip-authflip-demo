<?php

namespace App\Services;

use League\OAuth2\Client\Provider\GenericProvider;

class OAuthService
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new GenericProvider([
            'clientId'                => env('OAUTH_CLIENT_ID'),
            'clientSecret'            => env('OAUTH_CLIENT_SECRET'),
            'redirectUri'             => env('OAUTH_REDIRECT_URI'),
            'urlAuthorize'            => env('OAUTH_SERVER_URL') . '/oauth/authorize',
            'urlAccessToken'          => env('OAUTH_SERVER_URL') . '/oauth/token',
            'urlResourceOwnerDetails' => env('OAUTH_SERVER_URL') . '/api/user',
        ]);
    }

    public function getProvider()
    {
        return $this->provider;
    }
}
