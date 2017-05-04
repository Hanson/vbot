<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/14
 * Time: 11:21.
 */

namespace Hanson\Vbot\Core;

use Hanson\Vbot\Foundation\SyncFailException;
use Hanson\Vbot\Support\Console;

class Sync
{
    /**
     * get a message code.
     *
     * @param int $retry
     *
     * @return array
     */
    public function checkSync($retry = 0)
    {
        $url = server()->pushUri.'/synccheck?'.http_build_query([
            'r'        => time(),
            'sid'      => server()->sid,
            'uin'      => server()->uin,
            'skey'     => server()->skey,
            'deviceid' => server()->deviceId,
            'synckey'  => server()->syncKeyStr,
            '_'        => time(),
        ]);

        try {
            $content = http()->get($url, [], ['timeout' => 35]);

            preg_match('/window.synccheck=\{retcode:"(\d+)",selector:"(\d+)"\}/', $content, $matches);

            return [$matches[1], $matches[2]];
        } catch (\Exception $e) {
            print_r($e->getMessage());
            throw new SyncFailException();
        }
    }

    public function sync($retry = 0)
    {
        $url = sprintf(server()->baseUri.'/webwxsync?sid=%s&skey=%s&lang=zh_CN&pass_ticket=%s', server()->sid, server()->skey, server()->passTicket);

        try {
            $result = http()->json($url, [
                'BaseRequest' => server()->baseRequest,
                'SyncKey'     => server()->syncKey,
                'rr'          => ~time(),
            ], true, ['timeout' => 5]);

            if ($result['BaseResponse']['Ret'] == 0) {
                $this->generateSyncKey($result);

                return $result;
            } else {
                Console::log('ret:'.$result['BaseResponse']['Ret']);
                throw new \Exception();
            }
        } catch (\Exception $e) {
            print_r($e->getMessage());
            throw new SyncFailException();
        }
    }

    /**
     * generate a sync key.
     *
     * @param $result
     */
    public function generateSyncKey($result)
    {
        server()->syncKey = $result['SyncKey'];

        $syncKey = [];

        if (is_array(server()->syncKey['List'])) {
            foreach (server()->syncKey['List'] as $item) {
                $syncKey[] = $item['Key'].'_'.$item['Val'];
            }
        }

        server()->syncKeyStr = implode('|', $syncKey);
    }
}
