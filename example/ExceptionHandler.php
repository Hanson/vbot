<?php


namespace Hanson\Vbot\Example;


use Exception;
use Hanson\Vbot\Exceptions\FetchUuidException;
use Hanson\Vbot\Exceptions\LoginTimeoutException;

class ExceptionHandler
{

    public function handler(Exception $e)
    {
        if ($e instanceof FetchUuidException) {
            echo $e->getMessage();
        } elseif ($e instanceof LoginTimeoutException) {
            echo $e->getMessage();
        }

        return true;
    }

}