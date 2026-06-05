<?php

declare(strict_types=1);

$schemaPath = __DIR__ . '/../db/schema.sql';
$dbPath = __DIR__ . '/../db/pax.db';

if (!file_exists($schemaPath)) {
    echo "schema.sql not found at: $schemaPath\n";
    exit(1);
}

$schema = file_get_contents($schemaPath);

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec($schema);
    echo "DB created at: $dbPath\n";
} catch (Throwable $e) {
    echo "Error creating DB: " . $e->getMessage() . "\n";
    exit(1);
}
