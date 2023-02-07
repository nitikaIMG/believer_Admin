<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpException;

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
        return parent::render($request, $exception);

        // if( $exception->getStatusCode() ) {

        //     $status_code = $exception->getStatusCode();

        //     switch ($status_code) {
        //         case '404':
        //             return response()->view('errors.404');
        //             break;
        //         case '401':
        //             return response()->view('errors.401');
        //             break;
        //         case '419':
        //             return response()->view('errors.419');
        //             break;
        //         case '500':
        //             return response()->view('errors.500');
        //             break;            
        //         default:
        //             return response()->view('errors.error');
        //             break;
        //     }

        // } else {
            // return view('errors.error');
        // }
    }
}
