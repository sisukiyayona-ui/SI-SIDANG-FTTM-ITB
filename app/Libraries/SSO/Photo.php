<?php

namespace App\Libraries\SSO;

use GuzzleHttp\Exception\ClientException;

/**
 * Photo Model
 */
class Photo extends BaseModel
{
    public $data;

    function __construct()
    {
        $this->checkAuthentication();
        $this->fetch();
    }

    protected function fetch()
    {
        $url = '/me/photos/240x240/$value';
        try {
            $user = $this->graph()->createRequest("get", $url)
                ->execute();
        } catch (ClientException $e) {
            return false;
        }
        $this->data = base64_encode((string) $user->getRawBody());
        return $this->data;
    }
}
