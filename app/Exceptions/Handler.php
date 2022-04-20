<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
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
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            if (request()->is('admin/*')) {
                return redirect('/admin');
            } else {
                return redirect('/');
            }
        }

        if ($exception instanceof \Spatie\Permission\Exceptions\UnauthorizedException) {
            return $request->expectsJson() ? response()->json(['message' => $exception->getMessage()], 401) : abort(403, 'Unauthorized action.');
//            return response()->json([
//                'responseMessage' => 'You do not have the required authorization.',
//                'responseStatus'  => 403,
//            ]);
        }

        return parent::render($request, $exception);
    }

    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        $errorResults = array();
        $errors = $e->validator->errors()->getMessages();
        foreach ($errors as $errorKey => $error) {
            $errorResults[$errorKey] = is_array($error) ? $error[0] : $error;
        }

        return response()->responseError($errorResults);
    }

    protected function unauthenticated($request, \Illuminate\Auth\AuthenticationException $exception) {
        if (request()->is('admin/*')) {
            $route = 'login';
        } else {
            $route = 'shop.login';
        }
        return $request->expectsJson() ? response()->json([
            "status_code" => 401,
            "success" => false,
            'message' => $exception->getMessage()
        ], 401) : redirect()->route($route);
    }
}
