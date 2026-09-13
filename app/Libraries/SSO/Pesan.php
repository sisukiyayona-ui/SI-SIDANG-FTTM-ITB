<?php

namespace App\Libraries\SSO;

use GuzzleHttp\Exception\ClientException;

/**
 * Email Model
 */
class Pesan extends BaseModel
{
    public $data;

    function __construct()
    {
        $this->checkAuthentication();
        $this->fetch();
    }

    protected function fetch()
    {
        $url = '/me/mailFolders/Inbox/messages?$filter=isRead eq false&$select=subject,sender,webLink&$count=true';
        try {
            $msg = $this->graph()->createRequest("get", $url)
                ->execute();
        } catch (ClientException $e) {
            throw new \Exception("Cannot connect - redirect to login", 1);
        }
        $this->data['mail'] = $msg->getBody()['value'];

        $url = '/me/mailfolders';
        try {
            $msg = $this->graph()->createRequest("get", $url)
                ->execute();
        } catch (ClientException $e) {
            throw new \Exception("Cannot connect - redirect to login", 1);
        }
        $this->data['mailfolders'] = $msg->getBody()['value'];
        return $this->data;
    }
}
