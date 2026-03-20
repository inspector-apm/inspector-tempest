<?php

declare(strict_types=1);

namespace Inspector\Tempest;

use Inspector\Inspector;
use Tempest\Container\Container;

/**
 * Helper function to retrieve the current Inspector instance from the container.
 *
 * This provides a convenient way to access Inspector anywhere in the application
 * to add segments to the current transaction without requiring explicit dependency injection.
 *
 * Example usage:
 * ```php
 * use function Inspector\Tempest\inspector;
 *
 * inspector()->addSegment(function () {
 *     // Your code here
 * }, 'my-segment');
 * ```
 *
 * @return Inspector The Inspector instance
 */
function inspector(): Inspector
{
    static $inspector = null;
    static $container = null;

    if ($container === null) {
        $container = Container::instance();
    }

    if ($inspector === null && $container !== null) {
        $inspector = $container->get(Inspector::class);
    }

    return $inspector;
}
