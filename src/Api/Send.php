<?php

namespace Hanson\Vbot\Api;

use Hanson\Vbot\Message\Traits\SendAble;

class Send extends BaseApi
{
    public function needParams(): array
    {
        return ['type', 'username', 'content'];
    }

    public function handle($params): array
    {
        /** @var SendAble $class */
        $class = '\\Hanson\\Vbot\\Message\\'.ucfirst($params['type']);

        if (!class_exists($class)) {
            return $this->response('Class: '.$class.' not exist.', 500);
        }

        if (!method_exists(new $class(), 'send')) {
            return $this->response('Class: '.$class.' doesn\'t support send.', 500);
        }

        $params = array_merge([$params['username']], explode(',', $params['content']));

        return $this->response($class::send(...$params), 200);
    }
}
