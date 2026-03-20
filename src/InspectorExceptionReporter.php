<?php

namespace Inspector\Tempest;

use Inspector\Inspector;
use Tempest\Core\Exceptions\ExceptionReporter;
use Throwable;

class InspectorExceptionReporter implements ExceptionReporter
{
    public function __construct(
        protected Inspector $inspector
    ){
    }

    public function report(Throwable $throwable): void
    {
        $this->inspector->reportException($throwable);
    }
}
