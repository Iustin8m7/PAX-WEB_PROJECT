<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Database.php';

class AdminRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getVehicleRecordsCount(): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM vehicle_records';
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    public function getImportBatchesCount(): int
    {
        $sql = 'SELECT COUNT(*) AS total FROM import_batches';
        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    public function getLatestImportBatch(): ?array
    {
        $sql = '
            SELECT
                id,
                source_year,
                source_file,
                imported_at,
                rows_inserted,
                rows_rejected,
                notes
            FROM import_batches
            ORDER BY imported_at DESC, id DESC
            LIMIT 1
        ';

        $stmt = $this->pdo->query($sql);
        $row = $stmt->fetch();

        return $row !== false ? $row : null;
    }

    public function getRecentImportBatches(int $limit = 20): array
    {
        $sql = '
            SELECT
                id,
                source_year,
                source_file,
                imported_at,
                rows_inserted,
                rows_rejected,
                notes
            FROM import_batches
            ORDER BY imported_at DESC, id DESC
            LIMIT :limit
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getAvailableYears(): array
    {
        $sql = '
            SELECT DISTINCT year
            FROM vehicle_records
            ORDER BY year ASC
        ';

        $stmt = $this->pdo->query($sql);
        $rows = $stmt->fetchAll();

        return array_map(
            static fn(array $row): int => (int) $row['year'],
            $rows
        );
    }

    public function getImportBatchSummaryByYear(): array
    {
        $sql = '
            SELECT
                source_year,
                COUNT(*) AS batches_count,
                SUM(rows_inserted) AS total_rows_inserted,
                SUM(rows_rejected) AS total_rows_rejected
            FROM import_batches
            GROUP BY source_year
            ORDER BY source_year ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }
}