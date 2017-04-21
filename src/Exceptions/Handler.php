<?php


namespace Hanson\Vbot\Exceptions;

use Closure;
use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Support\Log;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Exception;
use ErrorException;

class Handler
{
    protected $dontReport = [
//        ConfigErrorException::class
    ];

    protected $systemException = [
        ConfigErrorException::class,
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
     * @return bool|mixed
     * @throws Exception
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
     * @param  \Exception $e
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
     * @throws FatalThrowableError
     * @throws Throwable
     */
    public function handleException(Throwable $e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        $isThrow = $this->report($e);

        $this->vbot->log->error($e->getMessage());

        if ($isThrow) {
            throw $e;
        }
    }

    private function errorMessage($message)
    {
//        return implode("\n", $message);
        return str_replace('#', "\n#", $message);
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
