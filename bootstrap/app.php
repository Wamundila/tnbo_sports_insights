<?php

use App\Http\Middleware\EnsureInsightsServiceToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'insights.auth' => EnsureInsightsServiceToken::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (ValidationException $exception, Request $request) {
            if (! $request->is('api/v1/*')) {
                return null;
            }

            $errors = $exception->errors();
            $message = $exception->getMessage() !== 'The given data was invalid.'
                ? $exception->getMessage()
                : collect($errors)->flatten()->first();

            $code = 'VALIDATION_ERROR';

            if ($request->is('api/v1/events/batch')) {
                $eventCount = count((array) $request->input('events', []));
                $code = $eventCount > 1000 ? 'EVENT_BATCH_TOO_LARGE' : 'EVENT_BATCH_INVALID';
                $message = $eventCount > 1000
                    ? 'Event batch exceeds the maximum size of 1000 events.'
                    : ($message ?: 'Event batch validation failed.');
            }

            return response()->json([
                'message' => $message ?: 'Validation failed.',
                'code' => $code,
                'errors' => $errors,
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        });

        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            if (! $request->is('api/v1/reports/*')) {
                return null;
            }

            return response()->json([
                'message' => 'Report not found.',
                'code' => 'REPORT_NOT_FOUND',
            ], Response::HTTP_NOT_FOUND);
        });

        $exceptions->render(function (Throwable $exception, Request $request) {
            if (! $request->is('api/v1/placements/resolve')) {
                return null;
            }

            report($exception);

            return response()->json([
                'message' => 'Placement resolution failed.',
                'code' => 'PLACEMENT_RESOLUTION_FAILED',
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        });
    })->create();
