<?php

namespace App\Exceptions;

use Exception;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Log;

use App\Exceptions\ModelException;


class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        if ($e instanceof \PDOException ) {
            Log::error(' FILE:..>'.$e->getFile().' LINE:...>'.$e->getLine().' DATABASE:...>'.$e->getCode().' Message:...>'.$e->getMessage());
            return;
        }
        if ($e instanceof \FatalErrorException ) {
            Log::error(' FILE:..>'.$e->getFile().' LINE:...>'.$e->getLine()
                    .' FATAL ERROR:...>'.$e->getCode()
                    .' Message:...>'.$e->getMessage()
                    . 'TRACE ....> '.$e->getTrace());
            return;
        }
        return parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($e instanceof ModelException) {
            return response()->view('errors.database', ['exceptionMessage' => $e->getMessage()], 500);
        }
        if ($e instanceof \PDOException ) {
            return response()->view('errors.database', ['exceptionMessage' => 'Database Error='.$e->getCode()], 500);
        }
        if ($e instanceof FatalErrorException ) {
            return response()->view('errors.fatal', ['exceptionMessage' => 'Fatal Error='.$e->getCode()], 500);
        }

        return response()->view('errors.fatal', ['exceptionMessage' => 'Unexpected System Error='.$e->getCode()], 500);
    }
}
