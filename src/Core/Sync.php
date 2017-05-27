<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/14
 * Time: 11:21.
 */

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Exceptions\WebSyncException;
use Hanson\Vbot\Foundation\Vbot;

class Sync
{
    /**
     * @var Vbot
     */
    private $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    /**
     * check if got a new message.
     *
     * @return array|bool
     */
    public function checkSync()
    {
        $content = $this->vbot->http->get($this->vbot->config['server.uri.push'].'/synccheck', ['timeout' => 35, 'query' => [
            'r'        => time(),
            'sid'      => $this->vbot->config['server.sid'],
            'uin'      => $this->vbot->config['server.uin'],
            'skey'     => $this->vbot->config['server.skey'],
            'deviceid' => $this->vbot->config['server.deviceId'],
            'synckey'  => $this->vbot->config['server.syncKeyStr'],
            '_'        => time(),
        ]]);

        if (!$content) {
            $this->vbot->console->log('checkSync no response');

            return false;
        }

        return preg_match('/window.synccheck=\{retcode:"(\d+)",selector:"(\d+)"\}/', $content, $matches) ?
            [$matches[1], $matches[2]] : false;
    }

    /**
     * get a message.
     *
     * @throws WebSyncException
     *
     * @return mixed|string
     */
    public function sync()
    {
        $url = sprintf($this->vbot->config['server.uri.base'].'/webwxsync?sid=%s&skey=%s&lang=zh_CN&pass_ticket=%s',
            $this->vbot->config['server.sid'],
            $this->vbot->config['server.skey'],
            $this->vbot->config['server.passTicket']
        );

        $result = $this->vbot->http->json($url, [
            'BaseRequest' => $this->vbot->config['server.baseRequest'],
            'SyncKey'     => $this->vbot->config['server.syncKey'],
            'rr'          => ~time(),
        ], true);

        if ($result && $result['BaseResponse']['Ret'] == 0) {
            $this->generateSyncKey($result);
        }

        return $result;
    }

    /**
     * generate a sync key.
     *
     * @param $result
     */
    public function generateSyncKey($result)
    {
        $this->vbot->config['server.syncKey'] = $result['SyncKey'];

        $syncKey = [];

        if (is_array($this->vbot->config['server.syncKey.List'])) {
            foreach ($this->vbot->config['server.syncKey.List'] as $item) {
                $syncKey[] = $item['Key'].'_'.$item['Val'];
            }
        }

        $this->vbot->config['server.syncKeyStr'] = implode('|', $syncKey);
    }
}
