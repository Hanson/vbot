<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:13
 */

namespace Hanson\Vbot\Core;

use GuzzleHttp\Client as HttpClient;
use Hanson\Vbot\Support\Console;

class Http
{

    static $instance;

    protected $client;

    /**
     * @return Http
     */
    public static function getInstance()
    {
        if(!static::$instance){
            static::$instance = new Http();
        }

        return static::$instance;
    }

    public function get($url, array $query = [], array $options = [])
    {
        if($query){
            $options['query'] = $query;
        }

        $options['connect_timeout'] = 60;

        return $this->request($url, 'GET', $options);
    }

    public function post($url, $query = [], $array = false)
    {
        $key = is_array($query) ? 'form_params' : 'body';

        $content = $this->request($url, 'POST', [$key => $query]);

        return $array ? json_decode($content, true) : $content;
    }

    public function json($url, $options = [], $array = false)
    {
        $content = $this->request($url, 'POST', ['json' => $options]);

        return $array ? json_decode($content, true) : $content;
    }

    public function setClient(HttpClient $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Return GuzzleHttp\Client instance.
     *
     * @return \GuzzleHttp\Client
     */
    public function getClient()
    {
        if (!($this->client instanceof HttpClient)) {
            $this->client = new HttpClient(['cookies' => true]);
        }

        return $this->client;
    }

    /**
     * @param $url
     * @param string $method
     * @param array $options
     * @return string
     */
    public function request($url, $method = 'GET', $options = [])
    {
        try{
            $response = $this->getClient()->request($method, $url, $options);
            return $response->getBody()->getContents();
        }catch (\Exception $e){
            Console::log('http链接失败：' . $e->getMessage(), Console::ERROR);
            Console::log('错误URL：' . $url, Console::ERROR);
        }

        return null;
    }


}