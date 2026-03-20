<?php

declare(strict_types=1);

namespace Inspector\Tempest;

use Inspector\Configuration;
use Inspector\Inspector;
use Tempest\Container\Container;
use Tempest\Core\KernelEvent;
use Tempest\EventBus\EventHandler;

/**
 * Service provider that registers the Inspector instance in the Tempest container.
 *
 * This provider hooks into the kernel boot process to make Inspector available
 * throughout the application for the current execution cycle.
 */
final readonly class InspectorServiceProvider
{
    public function __construct(
        private Container $container,
        private InspectorConfig $config,
    ) {
    }

    #[EventHandler(KernelEvent::BOOTED)]
    public function registerInspector(): void
    {
        // Register Inspector as a singleton in the container
        $this->container->register(
            Inspector::class,
            function (): \Inspector\Inspector {
                $config = new Configuration($this->config->ingestionKey);
                $config->setUrl($this->config->url);
                $config->setMaxItems($this->config->maxItems);
                $config->setTransport($this->config->transport);
                $config->setEnabled($this->config->enabled);
                $config->setVersion($this->config->version);

                return new Inspector($config);
            },
        );
    }
}
