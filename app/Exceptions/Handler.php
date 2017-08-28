<?php

namespace App\Exceptions;

use App\Exceptions\Middleware\MiddlewareException;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
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
        if (parent::shouldReport($e)) {
            $person = [];

            if (\Auth::check()) {
                $person = [
                    'id' => \Auth::user()->id,
                    'name' => \Auth::user()->name,
                    'email' => \Auth::user()->email,
                ];
            }

            \Log::error($e, [
                'person' => $person,
            ]);
        }
        parent::report($e);
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
        if ($e instanceof NotFoundHttpException && strpos($request->getRequestUri(), '/api') === 0) {
            return response()->json([
                'type'  => get_class($e),
                'error' => $e->getMessage()
            ], $e->getStatusCode());
        }

        if ($e instanceof MyException) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'type'  => get_class($e),
                    'error' => $e->getMessage()
                ], $e->getStatusCode());
            }
        }

        return parent::render($request, $e);
    }
    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $e
     * @return \Illuminate\Http\Response
     */
    protected function unauthenticated($request, AuthenticationException $e)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
        return redirect()->guest('login');
    }
}
