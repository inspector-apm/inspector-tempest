<?php

declare(strict_types=1);

namespace Inspector\Tempest;

use Inspector\Inspector;
use Tempest\Core\Exceptions\ExceptionReporter;
use Tempest\Core\ProvidesContext;
use Throwable;
use Exception;

class InspectorExceptionReporter implements ExceptionReporter
{
    public function __construct(
        protected Inspector $inspector
    ) {
    }

    /**
     * @throws Exception
     */
    public function report(Throwable $throwable): void
    {
        $error = $this->inspector->reportException($throwable);

        if ($throwable instanceof ProvidesContext) {
            $error->setContext($throwable->context());
        }
    }
}
