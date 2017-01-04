<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:13
 */

namespace Hanson\Robot\Core;

use GuzzleHttp\Client as HttpClient;

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

    public function get($url, array $options = [])
    {
        $query = $options ? ['query' => $options] : [];

        return $this->request($url, 'GET', $query);
    }

    public function post($url, $options = [], $array = false)
    {
        $key = is_array($options) ? 'form_params' : 'body';

        $content = $this->request($url, 'POST', [$key => $options]);

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

    public function request($url, $method = 'GET', $options = [])
    {
        $response = $this->getClient()->request($method, $url, $options);

        return $response->getBody()->getContents();
    }


}