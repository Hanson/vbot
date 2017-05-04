<?php
/**
 * Created by PhpStorm.
 * User: Hanson
 * Date: 2016/12/9
 * Time: 21:13.
 */

namespace Hanson\Vbot\Core;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Cookie\FileCookieJar;
use Hanson\Vbot\Support\Path;

class Http
{
    public static $instance;

    protected $client;

    /**
     * @var FileCookieJar;
     */
    protected $cookieJar;

    /**
     * @return Http
     */
    public static function getInstance()
    {
        if (!static::$instance) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    public function get($url, array $query = [], array $options = [])
    {
        if ($query) {
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

    public function json($url, $params = [], $array = false, $extra = [])
    {
        $params = array_merge(['json' => $params], $extra);

        $content = $this->request($url, 'POST', $params);

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
            $this->cookieJar = new FileCookieJar(Path::getCurrentSessionPath().'cookies', true);
            $this->client = new HttpClient(['cookies' => $this->cookieJar]);
        }

        return $this->client;
    }

    /**
     * @param $url
     * @param string $method
     * @param array  $options
     *
     * @return string
     */
    public function request($url, $method = 'GET', $options = [])
    {
        $response = $this->getClient()->request($method, $url, $options);

        if (is_dir(Path::getCurrentSessionPath())) {
            $this->cookieJar->save(Path::getCurrentSessionPath().'cookies');
        }

        return $response->getBody()->getContents();
    }
}
