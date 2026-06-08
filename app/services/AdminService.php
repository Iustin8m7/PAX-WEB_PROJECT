<?php

declare(strict_types=1);

require_once __DIR__ . '/../repositories/AdminRepository.php';

class AdminService
{
    private AdminRepository $adminRepository;
    private array $config;

    public function __construct()
    {
        $this->adminRepository = new AdminRepository();
        $this->config = require __DIR__ . '/../config/config.php';
    }

    public function getDashboardOverview(): array
    {
        $latestBatch = $this->adminRepository->getLatestImportBatch();
        $years = $this->adminRepository->getAvailableYears();

        if (isset($this->config['database']['path'])) {
            $databasePath = $this->config['database']['path'];
        } else {
            $databasePath = '';
        }

        if (isset($this->config['debug'])) {
            $debugEnabled = (bool) $this->config['debug'];
        } else {
            $debugEnabled = false;
        }

        return [
            'vehicle_records_count' => $this->adminRepository->getVehicleRecordsCount(),
            'import_batches_count' => $this->adminRepository->getImportBatchesCount(),
            'latest_import_batch' => $latestBatch,
            'available_years' => $years,
            'database_path' => $databasePath,
            'debug_enabled' => $debugEnabled,
        ];
    }

    public function getImportData(): array
    {
        return [
            'recent_batches' => $this->adminRepository->getRecentImportBatches(50),
            'summary_by_year' => $this->adminRepository->getImportBatchSummaryByYear(),
            'latest_batch' => $this->adminRepository->getLatestImportBatch(),
        ];
    }

    public function getLogData(): array
    {
        if (isset($this->config['paths']['logs'])) {
            $logsPath = $this->config['paths']['logs'];
        } else {
            $logsPath = '';
        }

        $importErrorsLog = rtrim($logsPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'import_errors.log';

        $exists = is_file($importErrorsLog);

        if ($exists) {
            $size = filesize($importErrorsLog);
        } else {
            $size = 0;
        }

        if ($exists) {
            $lines = $this->readLastLines($importErrorsLog, 100);
        } else {
            $lines = [];
        }

        if ($size !== false) {
            $logSizeBytes = (int) $size;
        } else {
            $logSizeBytes = 0;
        }

        return [
            'log_path' => $importErrorsLog,
            'log_exists' => $exists,
            'log_size_bytes' => $logSizeBytes,
            'lines' => $lines,
        ];
    }

    public function getSettingsData(): array
    {
        if (isset($this->config['app_name'])) {
            $appName = $this->config['app_name'];
        } else {
            $appName = 'Pax';
        }

        if (isset($this->config['debug'])) {
            $debug = (bool) $this->config['debug'];
        } else {
            $debug = false;
        }

        if (isset($this->config['paths'])) {
            $paths = $this->config['paths'];
        } else {
            $paths = [];
        }

        if (isset($this->config['database'])) {
            $database = $this->config['database'];
        } else {
            $database = [];
        }

        if (isset($this->config['app'])) {
            $app = $this->config['app'];
        } else {
            $app = [];
        }

        if (isset($this->config['admin']['username'])) {
            $adminUsername = $this->config['admin']['username'];
        } else {
            $adminUsername = 'admin';
        }

        return [
            'app_name' => $appName,
            'debug' => $debug,
            'paths' => $paths,
            'database' => $database,
            'app' => $app,
            'admin' => [
                'username' => $adminUsername,
            ],
        ];
    }

    private function readLastLines(string $filePath, int $limit = 100): array
    {
        $content = @file($filePath, FILE_IGNORE_NEW_LINES);

        if ($content === false) {
            return [];
        }

        return array_slice($content, -$limit);
    }
}