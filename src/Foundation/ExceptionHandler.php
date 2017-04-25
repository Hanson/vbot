<?php

namespace Hanson\Vbot\Foundation;

use Closure;
use ErrorException;
use Exception;
use Hanson\Vbot\Exceptions\ArgumentException;
use Hanson\Vbot\Exceptions\ConfigErrorException;
use Hanson\Vbot\Exceptions\LoginFailedException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;

class ExceptionHandler
{
    protected $dontReport = [
//        ConfigErrorException::class
    ];

    protected $systemException = [
        ConfigErrorException::class,
        LoginFailedException::class,
    ];

    /**
     * exception handler.
     *
     * @var Closure
     */
    protected $handler;

    /**
     * @var Vbot
     */
    protected $vbot;

    public function __construct(Vbot $vbot)
    {
        $this->vbot = $vbot;
    }

    /**
     * report while exception.
     *
     * @param Exception $e
     *
     * @throws Exception
     *
     * @return bool|mixed
     */
    public function report(Exception $e): bool
    {
        if ($this->shouldntReport($e)) {
            return true;
        }

        if ($this->handler) {
            return call_user_func_array($this->handler, [$e]);
        }

        return true;
    }

    /**
     * Determine if the exception is in the "do not report" list.
     *
     * @param \Exception $e
     *
     * @return bool
     */
    protected function shouldntReport(Exception $e)
    {
        foreach ($this->dontReport as $type) {
            return $e instanceof $type;
        }

        return false;
    }

    /**
     * set a exception handler.
     *
     * @param $closure
     *
     * @throws ArgumentException
     */
    public function setHandler($closure)
    {
        print_r($closure);
        if (!is_callable($closure)) {
            throw new ArgumentException('Argument must be callable.');
        }

        $this->handler = $closure;
    }

    /**
     * Convert PHP errors to ErrorException instances.
     *
     * @param int    $level
     * @param string $message
     * @param string $file
     * @param int    $line
     *
     * @throws \ErrorException
     *
     * @return void
     */
    public function handleError($level, $message, $file = '', $line = 0)
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * @param \Throwable $e
     *
     * @throws FatalThrowableError
     * @throws Throwable
     *
     * @return void
     */
    public function handleException(Throwable $e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        foreach ($this->systemException as $exception) {
            if ($e instanceof $exception) {
                throw $e;
            }
        }

        $isThrow = $this->report($e);

        $this->vbot->log->error($e->getMessage());

        if ($isThrow) {
            throw $e;
        }
    }

    /**
     * Handle the PHP shutdown event.
     *
     * @return void
     */
    public function handleShutdown()
    {
        if (!is_null($error = error_get_last()) && $this->isFatal($error['type'])) {
            $this->handleException($this->fatalExceptionFromError($error, 0));
        }
    }

    /**
     * Create a new fatal exception instance from an error array.
     *
     * @param array    $error
     * @param int|null $traceOffset
     *
     * @return FatalErrorException
     */
    protected function fatalExceptionFromError(array $error, $traceOffset = null)
    {
        return new FatalErrorException(
            $error['message'], $error['type'], 0, $error['file'], $error['line'], $traceOffset
        );
    }

    /**
     * Determine if the error type is fatal.
     *
     * @param int $type
     *
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }
}
