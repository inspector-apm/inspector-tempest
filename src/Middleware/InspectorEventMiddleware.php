<?php

declare(strict_types=1);

namespace Inspector\Tempest\Middleware;

use Inspector\Inspector;
use Tempest\Core\Priority;
use Tempest\EventBus\EventBusMiddleware;
use Tempest\EventBus\EventBusMiddlewareCallable;
use Throwable;

use function is_string;

#[Priority(Priority::HIGH)]
final readonly class InspectorEventMiddleware implements EventBusMiddleware
{
    public function __construct(
        protected Inspector $inspector,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function __invoke(string|object $event, EventBusMiddlewareCallable $next): void
    {
        if ($this->inspector->canAddSegments()) {
            $this->inspector->addSegment(function () use ($event, $next): void {
                $next($event);
            }, 'event', is_string($event) ? $event : $event::class);
        } else {
            $next($event);
        }
    }
}
