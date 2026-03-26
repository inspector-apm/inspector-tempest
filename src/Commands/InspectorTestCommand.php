<?php

declare(strict_types=1);

namespace Inspector\Tempest\Commands;

use Inspector\Configuration;
use Inspector\Inspector;
use Tempest\Console\Console;
use Tempest\Console\ConsoleCommand;
use Tempest\Console\ExitCode;
use Throwable;
use Exception;

use function function_exists;
use function proc_open;
use function usleep;

class InspectorTestCommand
{
    public function __construct(
        protected Inspector $inspector,
        protected Console $console
    ) {
    }

    /**
     * @throws Throwable
     */
    #[ConsoleCommand(name: 'inspector:test')]
    public function __invoke(): ExitCode
    {
        $this->console->info('Test Inspector configuration.');

        // Test proc_open function availability
        try {
            proc_open("", [], $pipes);
        } catch (Throwable) {
            $this->console->warning("❌ proc_open function disabled.");
            return ExitCode::CANCELLED;
        }

        if (function_exists('curl_version')) {
            $this->console->info('✅ CURL extension is enabled.');
        } else {
            $this->console->warning('❌ CURL is actually disabled so your app could not be able to send data to Inspector.');
        }

        // Check the status
        if (!$this->inspector->canAddSegments()) {
            $this->console->warning('❌ Inspector is not enabled');
            return ExitCode::CANCELLED;
        }
        $this->console->info('✅ Inspector enabled.');

        // Check the Ingestion Key
        $this->inspector->configure(function (Configuration $config): void {
            if ($config->getIngestionKey() === '') {
                $this->console->warning('INSPECTOR_INGESTION_KEY is not set in your .env file.');
            } else {
                $this->console->info('✅ Inspector key installed.');
            }
        });

        $this->inspector->addSegment(function (): void {
            usleep(500000);
        }, 'segment', 'POST http://localhost:8000/users');

        $this->inspector->addSegment(function (): void {
            usleep(300000);
        }, 'db.query', 'SELECT * FROM users');

        $this->inspector->reportException(new Exception('Your first exception'));

        $this->console->warning("Go to the dashboard to check the data: https://app.inspector.dev");

        return ExitCode::SUCCESS;
    }
}
