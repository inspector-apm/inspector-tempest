<?php

namespace Inspector\Tempest;

class InspectorConfig
{
    public function __construct(
        public ?string $ingestionKey = null,
        public string $url = 'https://ingest.inspector.dev',
        public bool $enabled = true,
        public int $maxItems = 150,
        public string $transport = 'async',
        public ?string $version = '1.0.0',
    ){
    }
}
