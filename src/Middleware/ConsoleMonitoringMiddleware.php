<?php

declare(strict_types=1);

namespace Inspector\Tempest\Middleware;

use Inspector\Inspector;
use Inspector\Models\Segment;
use Tempest\Console\ConsoleMiddleware;
use Tempest\Console\ConsoleMiddlewareCallable;
use Tempest\Console\ExitCode;
use Tempest\Console\Initializers\Invocation;
use Tempest\Console\Input\ConsoleInputArgument;
use Tempest\Core\Priority;
use Throwable;

use function array_map;
use function strtolower;

#[Priority(Priority::HIGH)]
class ConsoleMonitoringMiddleware implements ConsoleMiddleware
{
    public function __construct(
        protected Inspector $inspector
    ){
    }

    /**
     * @throws Throwable
     */
    public function __invoke(Invocation $invocation, ConsoleMiddlewareCallable $next): ExitCode|int
    {
        if ($this->inspector->canAddSegments()) {
            return $this->inspector->addSegment(function (Segment $segment) use ($invocation, $next): int|ExitCode {
                $segment->addContext('Arguments', array_map(fn(ConsoleInputArgument $argument) => ['name' => $argument->name, 'value' => $argument->value], $invocation->argumentBag->all()));

                return $next($invocation);
            }, 'command', $invocation->consoleCommand->getName());
        }

        $transaction = $this->inspector->startTransaction($invocation->consoleCommand->getName());

        $result = $next($invocation);

        if ($result instanceof ExitCode) {
            $transaction->setResult(strtolower($result->name));
        } else {
            $transaction->setResult(match ($result) {
                0 => 'success',
                default => 'failure',
            });
        }

        $this->inspector->flush();

        return $result;
    }
}
