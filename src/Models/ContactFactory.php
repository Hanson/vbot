<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/12
 * Time: 20:41
 */

namespace Hanson\Robot\Models;


use Hanson\Robot\Core\Server;

class ContactFactory
{



    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;
    }

    public function getContacts()
    {
        $url = sprintf(Server::BASE_URI . '/webwxgetcontact?pass_ticket=%s&skey=%s&r=%s', $this->server->passTicket, $this->server->skey, time());

        $content = $this->http->json($url, [
            'BaseRequest' => $this->baseRequest
        ]);
    }

}