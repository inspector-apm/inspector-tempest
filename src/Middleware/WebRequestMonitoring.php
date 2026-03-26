<?php

declare(strict_types=1);

namespace Inspector\Tempest\Middleware;

use Inspector\Inspector;
use Inspector\Tempest\Filters;
use Inspector\Tempest\InspectorConfig;
use Tempest\Core\Priority;
use Tempest\Http\Request;
use Tempest\Http\Response;
use Tempest\Router\HttpMiddleware;
use Tempest\Router\HttpMiddlewareCallable;
use Throwable;

use function strtoupper;
use function trim;

#[Priority(Priority::NORMAL)]
final readonly class WebRequestMonitoring implements HttpMiddleware
{
    public function __construct(
        protected Inspector $inspector,
        protected InspectorConfig $config,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(Request $request, HttpMiddlewareCallable $next): Response
    {
        if (!$this->shouldBeRecorded($request)) {
            return $next($request);
        }

        $transaction = $this->inspector->startTransaction($this->normalizeUri($request));
        $transaction->addContext('Request Body', $request->body);

        try {
            $response = $next($request);

            $transaction->addContext('Response', ['headers' => $response->headers])
                ->addContext('Response Body', $response->body);

            $transaction->setResult((string)$response->status->value);

            return $response;
        } catch (Throwable $e) {
            $this->inspector->reportException($e);
            throw $e;
        } finally {
            $this->inspector->flush();
        }
    }

    /**
     * Get the request PATH.
     */
    protected function normalizeUri(Request $request): string
    {
        return strtoupper($request->method->value) . ' /'.trim($request->path, '/');
    }

    protected function shouldBeRecorded(Request $request): bool
    {
        return Filters::isApprovedRequest(
            $this->config->ignoreUrls,
            $request->path,
        );
    }
}
