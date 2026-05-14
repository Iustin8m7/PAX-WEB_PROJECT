<?php

declare(strict_types=1);

return [
    'app_name' => 'Pax',
    'debug' => true,

    'paths' => [
        'project_root' => rtrim(getenv('HOME'), '/') . '/Projects/pax',
        'db' => rtrim(getenv('HOME'), '/') . '/Projects/pax/db/pax.db',
        'logs' => rtrim(getenv('HOME'), '/') . '/Projects/pax/logs',
        'geojson' => rtrim(getenv('HOME'), '/') . '/Projects/pax/raw_data/geojson/romania_counties.geojson',
    ],

    'database' => [
        'driver' => 'sqlite',
        'path' => rtrim(getenv('HOME'), '/') . '/Projects/pax/db/pax.db',
    ],

    'app' => [
        'default_year' => 2024,
        'min_year' => 2020,
        'max_year' => 2024,
        'default_page_size' => 25,
        'max_page_size' => 100,
    ],
];
