<?php
/**
 * Created by PhpStorm.
 * User: HanSon
 * Date: 2017/1/14
 * Time: 11:21
 */

namespace Hanson\Vbot\Core;


use Hanson\Vbot\Support\Console;

class Sync
{

    /**
     * get a message code
     *
     * @return array
     */
    public function checkSync()
    {
        $url = 'https://webpush.' . server()->domain . '/cgi-bin/mmwebwx-bin/synccheck?' . http_build_query([
                'r' => time(),
                'sid' => server()->sid,
                'uin' => server()->uin,
                'skey' => server()->skey,
                'deviceid' => server()->deviceId,
                'synckey' => server()->syncKeyStr,
                '_' => time()
            ]);

        $content = http()->get($url);

        try{
            preg_match('/window.synccheck=\{retcode:"(\d+)",selector:"(\d+)"\}/', $content, $matches);

            return [$matches[1], $matches[2]];
        }catch (\Exception $e){
            Console::log('Sync check return:' . $content);
            return [-1, -1];
        }
    }

    public function sync()
    {
        $url = sprintf(server()->baseUri . '/webwxsync?sid=%s&skey=%s&lang=en_US&pass_ticket=%s', server()->sid, server()->skey, server()->passTicket);

        try{
            $result = http()->json($url, [
                'BaseRequest' => server()->baseRequest,
                'SyncKey' => server()->syncKey,
                'rr' => ~time()
            ], true);

            if($result['BaseResponse']['Ret'] == 0){
                $this->generateSyncKey($result);
            }

            return $result;
        }catch (\Exception $e){
            return null;
        }
    }

    /**
     * generate a sync key
     *
     * @param $result
     */
    public function generateSyncKey($result)
    {
        server()->syncKey = $result['SyncKey'];

        $syncKey = [];

        if(is_array(server()->syncKey['List'])){
            foreach (server()->syncKey['List'] as $item) {
                $syncKey[] = $item['Key'] . '_' . $item['Val'];
            }
        }

        server()->syncKeyStr = implode('|', $syncKey);
    }

    /**
     * check message time
     *
     * @param $time
     */
    public function checkTime($time)
    {
        $checkTime = time() - $time;

        if($checkTime < 0.8){
            sleep(1 - $checkTime);
        }
    }

    /**
     * debug while the sync
     *
     * @param $retCode
     * @param $selector
     * @param null $sleep
     */
    public function debugMessage($retCode, $selector, $sleep = null)
    {
        Console::log('[DEBUG] retcode:' . $retCode . ' selector:' . $selector);

        if($sleep){
            sleep($sleep);
        }
    }
}