<?php


namespace Hanson\Vbot\Exceptions;


use Closure;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Exception;
use ErrorException;

class Handler
{

    /**
     * exception handler.
     *
     * @var Closure
     */
    protected $handler;

    /**
     * report while exception.
     *
     * @param Exception $e
     * @throws Exception
     */
    public function report(Exception $e)
    {
        $isThrow = true;

        if ($this->handler) {
            $isThrow = call_user_func_array($this->handler, [$e]);
        }

        if ($isThrow) {
            throw $e;
        }
    }

    /**
     * set a exception handler.
     *
     * @param Closure $closure
     */
    public function setHandler(Closure $closure)
    {
        $this->handler = $closure;
    }

    /**
     * Convert PHP errors to ErrorException instances.
     *
     * @param  int $level
     * @param  string $message
     * @param  string $file
     * @param  int $line
     * @return void
     *
     * @throws \ErrorException
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
     * @param  \Throwable $e
     * @return void
     */
    public function handleException(Throwable $e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        $this->report($e);
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
     * @param  array $error
     * @param  int|null $traceOffset
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
     * @param  int $type
     * @return bool
     */
    protected function isFatal($type)
    {
        return in_array($type, [E_COMPILE_ERROR, E_CORE_ERROR, E_ERROR, E_PARSE]);
    }

}