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

    protected $client;

    public function get($url, array $options = [])
    {
        $query = $options ? ['query' => $options] : [];

        return $this->request($url, 'GET', $query);
    }

    public function post($url, $options = [])
    {
        $key = is_array($options) ? 'form_params' : 'body';

        return $this->request($url, 'POST', [$key => $options]);
    }

    public function json($url, $options = [])
    {
        return $this->request($url, 'POST', ['json' => $options]);
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