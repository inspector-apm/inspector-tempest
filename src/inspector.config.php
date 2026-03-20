<?php

use function Tempest\env;

return new \Inspector\Tempest\InspectorConfig(
    ingestionKey: env('INSPECTOR_INGESTION_KEY'),
    url: env('INSPECTOR_URL', 'https://ingest.inspector.dev'),
    enabled: env('INSPECTOR_ENABLED', true),
    maxItems: env('INSPECTOR_MAX_ITEMS', 150),
    transport: env('INSPECTOR_TRANSPORT', 'async'),
    version: env('INSPECTOR_VERSION', '1.0.0'),
);
