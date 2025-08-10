<?php

use App\Http\Middleware\ManageUsersAccess;
use App\Http\Middleware\ValidateRequestHeaders;
use App\Utils\JsonResponseAPI;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->api()->alias([
            'validate.headers' => ValidateRequestHeaders::class,
            'manage.access' => ManageUsersAccess::class,
        ]);
        $middleware->api()->priority([
            ValidateRequestHeaders::class,
            ManageUsersAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (RouteNotFoundException $exceptions) {
            return JsonResponseAPI::errorResponse('Not Found', 404);
        });
        $exceptions->render(function (NotFoundHttpException $exceptions) {
            return JsonResponseAPI::errorResponse($exceptions->getMessage().' Not Found', 404);
        });
        $exceptions->render(function (ModelNotFoundException $exceptions) {
            return JsonResponseAPI::errorResponse(Str::afterLast($exceptions->getMessage(), '\\').' Not- Found', 404);
        });
    })->create();
