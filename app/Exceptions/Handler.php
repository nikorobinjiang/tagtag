<?php

namespace App\Exceptions;

use App\Libraries\BLogger;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        DistSdkException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        // dd([__CLASS__, $this, $exception->guards()]);
        $guards = [];
        if ($exception instanceof AuthenticationException) {
            if ($request->expectsJson()) {
                return response()->json(['message' => $exception->getMessage()], 401);
            } elseif (method_exists($exception, 'guards')) {
                $guards = $exception->guards();
                if (sizeof($guards) > 0) {
                    return redirect()->guest(route($guards[0] . '::login'));
                } else {
                    return redirect()->guest(route('login'));
                }
            } else {
                return redirect()->guest(route('login'));
            }
        } else if ($exception instanceof MaxAttemptsExceededException) {
            BLogger::scope(['queue', 'render'])->error('MaxAttempts', [
                'e' => $exception,
            ]);
            return parent::render($request, $exception);
        } else {
            return parent::render($request, $exception);
        }
    }
}
