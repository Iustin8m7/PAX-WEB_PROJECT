<?php

declare(strict_types=1);

$projectRoot = realpath(__DIR__ . '/../../');
if ($projectRoot === false) {
    throw new RuntimeException('Nu am putut determina calea proiectului.');
}
$dbPath = $projectRoot . '/db/pax.db';
$csvDir = $projectRoot . '/raw_data/csv';
$logDir = $projectRoot . '/logs';
$logPath = $logDir . '/import_errors.log';
$forceImport = in_array('--force', $argv ?? [], true) || in_array('-f', $argv ?? [], true);

if (!is_dir($logDir) && !mkdir($logDir, 0777, true) && !is_dir($logDir)) {
    throw new RuntimeException("Nu am putut crea directorul de log: $logDir");
}

$csvFiles = [
    'parc_auto_2020_combustibil.csv',
    'parc_auto_2021_combustibil.csv',
    'parc_auto_2022_combustibil.csv',
    'parc_auto_2023_combustibil.csv',
    'parc_auto_2024_combustibil.csv',
];

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA busy_timeout = 5000');
} catch (PDOException $e) {
    exit("Eroare la conectarea la baza de date: " . $e->getMessage() . PHP_EOL);
}

$schemaSqlPath = __DIR__ . '/../../db/schema.sql';
$seedSqlPath = __DIR__ . '/../../db/seed_counties.sql';
ensureDatabaseSchema($pdo, $schemaSqlPath);
ensureCountiesSeeded($pdo, $seedSqlPath);


function logError(string $logPath, string $message): void
{
    file_put_contents($logPath, $message . PHP_EOL, FILE_APPEND);
}

function cleanText(?string $value): string
{
    return trim((string) $value);
}

function cleanNullableText($value)
{
    $cleaned = trim((string) $value);

    if ($cleaned === '') {
        return null;
    }

    return $cleaned;
}

function extractYearFromFilename(string $filename): int
{
    if (preg_match('/(20\d{2})/', $filename, $matches)) {
        return (int) $matches[1];
    }

    throw new RuntimeException("Nu s-a putut extrage anul din numele fisierului: $filename");
}

function getExpectedHeader2020To2023(): array
{
    return [
        'JUDET',
        'CATEGORIE_NATIONALA',
        'CATEGORIE_COMUNITARA',
        'MARCA',
        'DESCRIERE_COMERCIALA',
        'VALUE_NAME',
        'TOTAL_VEHICULE',
    ];
}

function getExpectedHeader2024(): array
{
    return [
        'ID',
        'JUDET',
        'CATEGORIE_NATIONALA',
        'CATEGORIE_COMUNITARA',
        'MARCA',
        'DESCRIERE_COMERCIALA',
        'VALUE_NAME',
        'TOTAL_VEHICULE',
    ];
}

function getExpectedHeaderForYear(int $year): array
{
    if ($year === 2024) {
        return getExpectedHeader2024();
    }

    return getExpectedHeader2020To2023();
}

function getDelimiterForYear(int $year): string
{
    if ($year === 2024) {
        return ',';
    }

    return ';';
}

function isExpectedHeaderForYear(array $row, int $year): bool
{
    return $row === getExpectedHeaderForYear($year);
}


function createImportBatch(PDO $pdo, int $sourceYear, string $sourceFile): int
{
    $importedAt = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare('
        INSERT INTO import_batches (source_year, source_file, imported_at, rows_inserted, rows_rejected, notes)
        VALUES (:source_year, :source_file, :imported_at, 0, 0, NULL)
    ');

    $stmt->execute([
        ':source_year' => $sourceYear,
        ':source_file' => $sourceFile,
        ':imported_at' => $importedAt,
    ]);

    return (int) $pdo->lastInsertId();
}

function updateImportBatch(PDO $pdo, int $batchId, int $rowsInserted, int $rowsRejected): void
{
    $stmt = $pdo->prepare('
        UPDATE import_batches
        SET rows_inserted = :rows_inserted,
            rows_rejected = :rows_rejected
        WHERE id = :id
    ');

    $stmt->execute([
        ':rows_inserted' => $rowsInserted,
        ':rows_rejected' => $rowsRejected,
        ':id' => $batchId,
    ]);
}

function importBatchExists(PDO $pdo, string $sourceFile): bool
{
    $stmt = $pdo->prepare('SELECT rows_inserted, rows_rejected FROM import_batches WHERE source_file = :source_file LIMIT 1');
    $stmt->execute([':source_file' => $sourceFile]);
    $batch = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($batch === false) {
        return false;
    }

    $rowsInserted = (int) $batch['rows_inserted'];
    $rowsRejected = (int) $batch['rows_rejected'];

    if ($rowsInserted === 0 && $rowsRejected === 0) {
        $deleteStmt = $pdo->prepare('DELETE FROM import_batches WHERE source_file = :source_file');
        $deleteStmt->execute([':source_file' => $sourceFile]);
        return false;
    }

    return true;
}

function getOrCreateLookupId(PDO $pdo, string $tableName, string $value): int
{
    $allowedTables = [
        'vehicle_categories',
        'community_categories',
        'fuel_types',
        'brands',
    ];

    if (!in_array($tableName, $allowedTables, true)) {
        throw new InvalidArgumentException("Tabela nepermisa: $tableName");
    }

    $selectSql = "SELECT id FROM {$tableName} WHERE name = :name LIMIT 1";
    $selectStmt = $pdo->prepare($selectSql);
    $selectStmt->execute([':name' => $value]);

    $existingId = $selectStmt->fetchColumn();
    if ($existingId !== false) {
        return (int) $existingId;
    }

    $insertSql = "INSERT INTO {$tableName} (name) VALUES (:name)";
    $insertStmt = $pdo->prepare($insertSql);
    $insertStmt->execute([':name' => $value]);

    return (int) $pdo->lastInsertId();
}
function getCountyId(PDO $pdo, string $countyName): int
{
    $stmt = $pdo->prepare('SELECT id FROM counties WHERE name = :name LIMIT 1');
    $stmt->execute([':name' => $countyName]);

    $id = $stmt->fetchColumn();
    if ($id === false) {
        throw new RuntimeException("Judetul nu exista in tabela counties: $countyName");
    }

    return (int) $id;
}

function loadCountySeedFile(PDO $pdo, string $seedSqlPath): void
{
    if (!file_exists($seedSqlPath)) {
        throw new RuntimeException("Fisierul de seed pentru judete nu exista: $seedSqlPath");
    }

    $sql = file_get_contents($seedSqlPath);
    if ($sql === false) {
        throw new RuntimeException("Nu s-a putut citi fisierul de seed: $seedSqlPath");
    }

    $pdo->beginTransaction();
    $pdo->exec($sql);
    $pdo->commit();
}

function ensureDatabaseSchema(PDO $pdo, string $schemaSqlPath): void
{
    $tableExists = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='counties' LIMIT 1")->fetchColumn();
    if ($tableExists === false) {
        if (!file_exists($schemaSqlPath)) {
            throw new RuntimeException("Fisierul schema.sql nu exista: $schemaSqlPath");
        }

        $sql = file_get_contents($schemaSqlPath);
        if ($sql === false) {
            throw new RuntimeException("Nu s-a putut citi schema.sql: $schemaSqlPath");
        }

        echo "[SCHEMA] Initializare baza de date cu schema din $schemaSqlPath\n";
        $pdo->beginTransaction();
        $pdo->exec($sql);
        $pdo->commit();
    }
}

function ensureCountiesSeeded(PDO $pdo, string $seedSqlPath): void
{
    $rowCount = (int) $pdo->query('SELECT count(*) FROM counties')->fetchColumn();
    if ($rowCount === 0) {
        echo "[SEED] Popularea tabelului counties\n";
        loadCountySeedFile($pdo, $seedSqlPath);
    }
}

function normalizeCountyName(string $countyName): string
{
    $replacements = [
        'CARAS SEVERIN' => 'CARAS-SEVERIN',
        'DIMBOVITA' => 'DAMBOVITA',
        'VILCEA' => 'VALCEA',
    ];

    if (isset($replacements[$countyName])) {
        return $replacements[$countyName];
    }

    return $countyName;
}

$insertVehicleRecordStmt = $pdo->prepare('
    INSERT INTO vehicle_records (
        year,
        county_id,
        national_category_id,
        community_category_id,
        brand_id,
        model_description,
        fuel_type_id,
        vehicle_count,
        import_batch_id
    ) VALUES (
        :year,
        :county_id,
        :national_category_id,
        :community_category_id,
        :brand_id,
        :model_description,
        :fuel_type_id,
        :vehicle_count,
        :import_batch_id
    )
');

function importCsvFile(PDO $pdo, string $csvPath, string $csvFilename, string $logPath, PDOStatement $insertVehicleRecordStmt, bool $forceImport = false): void
{
    $year = extractYearFromFilename($csvFilename);
    $csvDelimiter = getDelimiterForYear($year);
    $expectedHeader = getExpectedHeaderForYear($year);

    if (importBatchExists($pdo, $csvFilename)) {
        if ($forceImport) {
            echo "[FORCE] Reimport fișier: {$csvFilename}" . PHP_EOL;
        } else {
            echo "[SKIP] Fisier deja importat: {$csvFilename}" . PHP_EOL;
            return;
        }
    }

    $batchId = createImportBatch($pdo, $year, $csvFilename);
    $pdo->beginTransaction();
    $rowsInserted = 0;
    $rowsRejected = 0;

    $handle = fopen($csvPath, 'r');
    if ($handle === false) {
        throw new RuntimeException("Nu s-a putut deschide fisierul: $csvPath");
    }

    $firstRow = fgetcsv($handle, 0, $csvDelimiter);
    if ($firstRow === false) {
        fclose($handle);
        throw new RuntimeException("Fisierul este gol sau invalid: $csvFilename");
    }

    if (isset($firstRow[0])) {
        $firstRow[0] = preg_replace('/^\xEF\xBB\xBF/', '', $firstRow[0]);
    }

    $header = $expectedHeader;
    $pendingRow = null;
    $rowNumber = 0;

    if (isExpectedHeaderForYear($firstRow, $year)) {
        $rowNumber = 1;
    } else {
        $pendingRow = $firstRow;
    }

    while (true) {
        if ($pendingRow !== null) {
            $row = $pendingRow;
            $pendingRow = null;
            $rowNumber++;
        } else {
            $row = fgetcsv($handle, 0, $csvDelimiter);
            if ($row === false) {
                break;
            }
            $rowNumber++;
        }

        try {
            if (count($row) !== count($header)) {
                throw new RuntimeException('Numarul de coloane din rand nu corespunde cu headerul.');
            }

            if (isExpectedHeaderForYear($row, $year)) {
                continue;
            }

            $assoc = array_combine($header, $row);
            if ($assoc === false) {
                throw new RuntimeException("Nu s-a putut combina headerul cu randul curent.");
            }

            $countyValue = null;
            if (isset($assoc['JUDET'])) {
                $countyValue = $assoc['JUDET'];
            }
            $countyName = cleanText($countyValue);

            $nationalCategoryValue = null;
            if (isset($assoc['CATEGORIE_NATIONALA'])) {
                $nationalCategoryValue = $assoc['CATEGORIE_NATIONALA'];
            }
            $nationalCategory = cleanText($nationalCategoryValue);

            $communityCategoryValue = null;
            if (isset($assoc['CATEGORIE_COMUNITARA'])) {
                $communityCategoryValue = $assoc['CATEGORIE_COMUNITARA'];
            }
            $communityCategory = cleanText($communityCategoryValue);

            $brandValue = null;
            if (isset($assoc['MARCA'])) {
                $brandValue = $assoc['MARCA'];
            }
            $brandName = cleanText($brandValue);

            $modelValue = null;
            if (isset($assoc['DESCRIERE_COMERCIALA'])) {
                $modelValue = $assoc['DESCRIERE_COMERCIALA'];
            }
            $modelDescription = cleanNullableText($modelValue);

            $fuelValue = null;
            if (isset($assoc['VALUE_NAME'])) {
                $fuelValue = $assoc['VALUE_NAME'];
            }
            $fuelType = cleanText($fuelValue);

            $vehicleCountValue = null;
            if (isset($assoc['TOTAL_VEHICULE'])) {
                $vehicleCountValue = $assoc['TOTAL_VEHICULE'];
            }
            $vehicleCountRaw = cleanText($vehicleCountValue);

            if (
                $countyName === '' ||
                $nationalCategory === '' ||
                $communityCategory === '' ||
                $brandName === '' ||
                $fuelType === '' ||
                $vehicleCountRaw === ''
            ) {
                throw new RuntimeException('Unul sau mai multe campuri obligatorii sunt goale.');
            }

            if (!is_numeric($vehicleCountRaw)) {
                throw new RuntimeException("TOTAL_VEHICULE nu este numeric: {$vehicleCountRaw}");
            }

            $vehicleCount = (int) $vehicleCountRaw;
            $countyName = normalizeCountyName(strtoupper($countyName));
            $countyId = getCountyId($pdo, $countyName);
            $nationalCategoryId = getOrCreateLookupId($pdo, 'vehicle_categories', $nationalCategory);
            $communityCategoryId = getOrCreateLookupId($pdo, 'community_categories', $communityCategory);
            $brandId = getOrCreateLookupId($pdo, 'brands', $brandName);
            $fuelTypeId = getOrCreateLookupId($pdo, 'fuel_types', $fuelType);

            $insertVehicleRecordStmt->execute([
                ':year' => $year,
                ':county_id' => $countyId,
                ':national_category_id' => $nationalCategoryId,
                ':community_category_id' => $communityCategoryId,
                ':brand_id' => $brandId,
                ':model_description' => $modelDescription,
                ':fuel_type_id' => $fuelTypeId,
                ':vehicle_count' => $vehicleCount,
                ':import_batch_id' => $batchId,
            ]);

            $rowsInserted++;
        } catch (Throwable $e) {
            $rowsRejected++;
            logError($logPath, '[' . $csvFilename . '][row ' . $rowNumber . '] ' . $e->getMessage());
        }
    }

    fclose($handle);

    updateImportBatch($pdo, $batchId, $rowsInserted, $rowsRejected);
    $pdo->commit();
    echo "[OK] {$csvFilename}: inserate={$rowsInserted}, respinse={$rowsRejected}" . PHP_EOL;
}


foreach ($csvFiles as $csvFilename) {
    $csvPath = $csvDir . '/' . $csvFilename;

    if (!file_exists($csvPath)) {
        echo "[LIPSA] Fisier inexistent: {$csvPath}" . PHP_EOL;
        continue;
    }

    try {
        importCsvFile($pdo, $csvPath, $csvFilename, $logPath, $insertVehicleRecordStmt, $forceImport);
    } catch (Throwable $e) {
        logError($logPath, '[FATAL][' . $csvFilename . '] ' . $e->getMessage());
        echo "[EROARE] {$csvFilename}: " . $e->getMessage() . PHP_EOL;
    }
}

echo "Import finalizat." . PHP_EOL;

