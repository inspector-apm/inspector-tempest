<?php

namespace Inspector\Tempest;

use Tempest\Core\Installer;
use Tempest\Core\PublishesFiles;
use function Tempest\src_path;

class InspectorInstaller implements Installer
{
    use PublishesFiles;

    private(set) string $name = 'inspector';

    public function install(): void
    {
        $this->publish(
            source: __DIR__ . '/inspector.config.php',
            destination: src_path('inspector.config.php'),
        );

        $this->publishImports();
    }
}
