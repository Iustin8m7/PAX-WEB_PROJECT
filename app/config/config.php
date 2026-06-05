<?php

declare(strict_types=1);

// Compute sensible defaults based on either the user's HOME (developer machines)
// or the project root. This makes the app work on Windows and other setups
// where HOME/Projects/pax is not present.
$projectRoot = dirname(__DIR__, 2);
$home = getenv('HOME') ?: getenv('USERPROFILE');
$homeProjectsCandidate = $home ? rtrim($home, '/') . '/Projects/pax' : null;

$defaultDbPath = $projectRoot . '/db/pax.db';
$defaultGeojson = $projectRoot . '/raw_data/geojson/romania_counties.geojson';

// Prefer the project-local DB unless a HOME/Projects/pax DB actually exists.
$useHomeProjects = $homeProjectsCandidate && file_exists($homeProjectsCandidate . '/db/pax.db');

return [
    'app_name' => 'Pax',
    'debug' => true,

    'paths' => [
        'project_root' => $useHomeProjects ? $homeProjectsCandidate : $projectRoot,
        'db' => $useHomeProjects ? $homeProjectsCandidate . '/db/pax.db' : $defaultDbPath,
        'logs' => $useHomeProjects ? $homeProjectsCandidate . '/logs' : $projectRoot . '/logs',
        'geojson' => $useHomeProjects ? $homeProjectsCandidate . '/raw_data/geojson/romania_counties.geojson' : $defaultGeojson,
    ],

    'database' => [
        'driver' => 'sqlite',
        'path' => $useHomeProjects ? $homeProjectsCandidate . '/db/pax.db' : $defaultDbPath,
    ],

    'app' => [
        'default_year' => 2024,
        'min_year' => 2020,
        'max_year' => 2024,
        'default_page_size' => 25,
        'max_page_size' => 100,
    ],
];
