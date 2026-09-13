<?php

namespace App\Libraries\SSO;

use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\User;
use Illuminate\Support\Facades\Session;

/**
 * Base Model - Microsoft Graph API
 */
class BaseModel
{
    protected $graph;

    public function graph()
    {
        $this->graph = new Graph();
        $this->graph->setApiVersion('beta');

        // Prioritas: SSOITB session > sso_access_token dari login
        $token = Session::get('SSOITB.accessToken') ?? session('sso_access_token');
        $this->graph->setAccessToken($token);

        return $this->graph;
    }

    public function checkAuthentication(): bool
    {
        $token = Session::get('SSOITB.accessToken') ?? session('sso_access_token');
        if (!$token) {
            return false;
        }

        try {
            $user = $this->graph()->createRequest("get", "/me")
                ->setReturnType(User::class)
                ->execute();
        } catch (ClientException $e) {
            return false;
        }
        return (null !== $user->getGivenName());
    }
}
