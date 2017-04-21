<?php

namespace Hanson\Vbot\Foundation\Bootstrap;

use ErrorException;
use Exception;
use Hanson\Vbot\Foundation\Vbot;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class HandleExceptions
{
    protected $app;

    /**
     * Bootstrap the given application.
     *
     * @param Vbot $app
     *
     * @return void
     */
    public function bootstrap(Vbot $app)
    {
        $this->app = $app;
        error_reporting(-1);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Convert PHP errors to ErrorException instances.
     *
     * @param int    $level
     * @param string $message
     * @param string $file
     * @param int    $line
     * @param array  $context
     *
     * @throws \ErrorException
     *
     * @return void
     */
    public function handleError($level, $message, $file = '', $line = 0, $context = [])
    {
        if (error_reporting() & $level) {
            throw new ErrorException($message, 0, $level, $file, $line);
        }
    }

    /**
     * Handle an uncaught exception from the application.
     *
     * Note: Most exceptions can be handled via the try / catch block in
     * the HTTP and Console kernels. But, fatal error exceptions must
     * be handled differently since they are not normal exceptions.
     *
     * @param \Throwable $e
     *
     * @return void
     */
    public function handleException($e)
    {
        if (!$e instanceof Exception) {
            $e = new FatalThrowableError($e);
        }

        $this->getExceptionHandler()->report($e);
        if ($this->app->runningInConsole()) {
            $this->renderForConsole($e);
        } else {
            $this->renderHttpResponse($e);
        }
    }
}
