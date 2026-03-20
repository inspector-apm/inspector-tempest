<?php

declare(strict_types=1);

namespace Inspector\Tempest;

use Inspector\Inspector;
use Tempest\Core\Priority;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;

/**
 * HTTP middleware that intercepts Tempest HTTP requests and sends monitoring data to Inspector.
 *
 * This middleware retrieves the Inspector instance from the Tempest container (which is
 * registered by InspectorServiceProvider), creates a transaction for each request, captures
 * request/response data, and flushes the data at the end of the cycle.
 *
 * The Inspector instance is available throughout the application via dependency injection,
 * allowing other parts of the code to add segments to the current transaction.
 */
#[Priority(Priority::NORMAL)]
final readonly class InspectorMiddleware implements HttpMiddleware
{
    public function __construct(
        private Inspector $inspector,
    ) {}

    /**
     * @throws \Throwable
     */
    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        $transaction = $this->inspector->startTransaction($this->normalizeUri($request));
        $transaction->addContext('Headers', $request->headers->toArray());

        try {
            $response = $next($request);

            $transaction->addContext('Response', [
                'status_code' => $response->status->value,
            ]);

            return $response;
        } catch (\Throwable $e) {
            $this->inspector->reportException($e);
            throw $e;
        } finally {
            $this->inspector->flush();
        }
    }

    /**
     * Get the request PATH.
     */
    private function normalizeUri(Request $request): string
    {
        return strtoupper($request->method->value) . ' /'.trim($request->uri, '/');
    }
}
