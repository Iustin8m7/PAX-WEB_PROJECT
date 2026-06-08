<?php

declare(strict_types=1);

// Compute sensible defaults based on either the user's HOME developer machines
// or the project root. This makes the app work on Windows and other setups
// where HOME/Projects/pax is not present.
$projectRoot = dirname(__DIR__, 2);

$home = getenv('HOME');

if (!$home) {
    $home = getenv('USERPROFILE');
}

if ($home) {
    $homeProjectsCandidate = rtrim($home, '/\\') . '/Projects/pax';
} else {
    $homeProjectsCandidate = null;
}

$defaultDbPath = $projectRoot . '/db/pax.db';
$defaultGeojson = $projectRoot . '/raw_data/geojson/romania_counties.geojson';

// Prefer the HOME/Projects/pax DB only if it actually exists.
$useHomeProjects = false;

if ($homeProjectsCandidate && file_exists($homeProjectsCandidate . '/db/pax.db')) {
    $useHomeProjects = true;
}

if ($useHomeProjects) {
    $selectedProjectRoot = $homeProjectsCandidate;
    $selectedDbPath = $homeProjectsCandidate . '/db/pax.db';
    $selectedLogsPath = $homeProjectsCandidate . '/logs';
    $selectedGeojsonPath = $homeProjectsCandidate . '/raw_data/geojson/romania_counties.geojson';
} else {
    $selectedProjectRoot = $projectRoot;
    $selectedDbPath = $defaultDbPath;
    $selectedLogsPath = $projectRoot . '/logs';
    $selectedGeojsonPath = $defaultGeojson;
}

return [
    'app_name' => 'Pax',
    'debug' => false,

    'paths' => [
        'project_root' => $selectedProjectRoot,
        'db' => $selectedDbPath,
        'logs' => $selectedLogsPath,
        'geojson' => $selectedGeojsonPath,
    ],

    'database' => [
        'driver' => 'sqlite',
        'path' => $selectedDbPath,
    ],

    'app' => [
        'default_year' => 2024,
        'min_year' => 2020,
        'max_year' => 2024,
        'default_page_size' => 25,
        'max_page_size' => 100,
    ],

     'admin' => [
        'username' => 'admin',
        'password' => 'admin123',
    ],
];