<?php

declare(strict_types=1);

require_once __DIR__ . '/../repositories/VehicleRepository.php';
require_once __DIR__ . '/../repositories/StatisticsRepository.php';
require_once __DIR__ . '/../repositories/MapRepository.php';

class ExportService
{
    private VehicleRepository $vehicleRepository;
    private StatisticsRepository $statisticsRepository;
    private MapRepository $mapRepository;

    public function __construct()
    {
        $this->vehicleRepository = new VehicleRepository();
        $this->statisticsRepository = new StatisticsRepository();
        $this->mapRepository = new MapRepository();
    }

    public function getAllowedResources(): array
    {
        return [
            'search', ///rezultate de cautare
            'statistics', ///date de agregare statistice
            'map',///datele pentru componenta cartografica
        ];
    }

    public function getAllowedFormats(): array
    {
        return [
            'csv',
        ];
    }

    public function validateResource(string $resource): string
    {
        $allowed = $this->getAllowedResources();

        if (!in_array($resource, $allowed, true)) {
            throw new InvalidArgumentException('Resursa cerută pentru export nu este permisă.');
        }

        return $resource;
    }

    public function validateFormat(string $format): string
    {
        $allowed = $this->getAllowedFormats();

        if (!in_array($format, $allowed, true)) {
            throw new InvalidArgumentException('Formatul cerut pentru export nu este permis.');
        }

        return $format;
    }

    public function buildExportPayload( ///construiește pachetul de date pentru export
        string $resource, ///tipul de export
        string $format, ///formatul cerut
        array $params = [] ///filtre si optiuni
    ): array {
        $resource = $this->validateResource($resource);
        $format = $this->validateFormat($format);

        if ($format !== 'csv') {
            throw new InvalidArgumentException('Momentan este suportat doar exportul CSV.');
        }

        if ($resource === 'search') {
            return $this->buildSearchExport($params);
        }

        if ($resource === 'statistics') {
            return $this->buildStatisticsExport($params);
        }

        if ($resource === 'map') {
            return $this->buildMapExport($params);
        }

        throw new InvalidArgumentException('Resursa de export nu este implementată.');
    }

    private function buildSearchExport(array $params): array ///Construiește payload-ul de export 
                                                               /// pentru rezultatele de căutare.
    {
        $filters = [
            'year' => $this->getArrayValue($params, 'year', null),
            'county_code' => $this->getArrayValue($params, 'county_code', null),
            'national_category' => $this->getArrayValue($params, 'national_category', null),
            'community_category' => $this->getArrayValue($params, 'community_category', null),
            'brand' => $this->getArrayValue($params, 'brand', null),
            'fuel_type' => $this->getArrayValue($params, 'fuel_type', null),
            'model' => $this->getArrayValue($params, 'model', null),
        ];

        $sort = $this->getArrayValue($params, 'sort_by', 'vehicle_count');
        $order = $this->getArrayValue($params, 'sort_order', 'desc');

        $rows = $this->vehicleRepository->search(
            $filters,
            1,
            100,
            $sort,
            $order
        );

        $headers = [
            'ID',
            'An',
            'Cod județ',
            'Județ',
            'Categorie națională',
            'Categorie comunitară',
            'Marcă',
            'Model comercial',
            'Combustibil',
            'Total vehicule',
        ];

        $csvRows = [];

        foreach ($rows as $row) {
            $csvRows[] = [
                $this->getArrayValue($row, 'id', ''),
                $this->getArrayValue($row, 'year', ''),
                $this->getArrayValue($row, 'county_code', ''),
                $this->getArrayValue($row, 'county_name', ''),
                $this->getArrayValue($row, 'national_category', ''),
                $this->getArrayValue($row, 'community_category', ''),
                $this->getArrayValue($row, 'brand_name', ''),
                $this->getArrayValue($row, 'model_description', ''),
                $this->getArrayValue($row, 'fuel_type', ''),
                $this->getArrayValue($row, 'vehicle_count', ''),
            ];
        }

        return [ ///returnarea payload-ului
            'filename' => $this->buildFileName('search_export'),
            'content_type' => 'text/csv; charset=utf-8',
            'content' => $this->convertRowsToCsv($headers, $csvRows),
        ];
    }

    private function buildStatisticsExport(array $params): array ///Construieste exportul pentru date statistice
    {
        $view = $this->getArrayValue($params, 'view', 'overview');

        $filters = [
            'year' => $this->getArrayValue($params, 'year', null),
            'county_code' => $this->getArrayValue($params, 'county_code', null),
            'national_category' => $this->getArrayValue($params, 'national_category', null),
            'community_category' => $this->getArrayValue($params, 'community_category', null),
            'fuel_type' => $this->getArrayValue($params, 'fuel_type', null),
            'brand' => $this->getArrayValue($params, 'brand', null),
        ];

        if ($view === 'overview') {
            $result = $this->statisticsRepository->getOverview($filters);

            $headers = [
                'An',
                'Total vehicule',
                'Număr județe',
                'Număr mărci',
                'Număr tipuri combustibil',
                'Număr categorii naționale',
            ];

            $rows = [[
                $this->getArrayValue($result, 'year', ''),
                $this->getArrayValue($result, 'total_vehicles', 0),
                $this->getArrayValue($result, 'counties_count', 0),
                $this->getArrayValue($result, 'brands_count', 0),
                $this->getArrayValue($result, 'fuel_types_count', 0),
                $this->getArrayValue($result, 'national_categories_count', 0),
            ]];

            return [
                'filename' => $this->buildFileName('statistics_overview'),
                'content_type' => 'text/csv; charset=utf-8',
                'content' => $this->convertRowsToCsv($headers, $rows),
            ];
        }

        if ($view === 'yearly-totals') {
            $result = $this->statisticsRepository->getYearlyTotals($filters);

            $headers = [
                'An',
                'Total vehicule',
            ];

            $rows = [];

            foreach ($result as $row) {
                $rows[] = [
                    $this->getArrayValue($row, 'year', ''),
                    $this->getArrayValue($row, 'total_vehicles', 0),
                ];
            }

            return [
                'filename' => $this->buildFileName('statistics_yearly_totals'),
                'content_type' => 'text/csv; charset=utf-8',
                'content' => $this->convertRowsToCsv($headers, $rows),
            ];
        }

        if ($view === 'top-brands') {
            if (isset($params['limit'])) {
                $limit = (int) $params['limit'];
            } else {
                $limit = 10;
            }

            $result = $this->statisticsRepository->getTopBrands($filters, $limit);

            $headers = [
                'ID marcă',
                'Marcă',
                'Total vehicule',
            ];

            $rows = [];

            foreach ($result as $row) {
                $rows[] = [
                    $this->getArrayValue($row, 'id', ''),
                    $this->getArrayValue($row, 'name', ''),
                    $this->getArrayValue($row, 'total_vehicles', 0),
                ];
            }

            return [
                'filename' => $this->buildFileName('statistics_top_brands'),
                'content_type' => 'text/csv; charset=utf-8',
                'content' => $this->convertRowsToCsv($headers, $rows),
            ];
        }

        if ($view === 'fuel-distribution') {
            $result = $this->statisticsRepository->getFuelDistribution($filters);

            $headers = [
                'ID combustibil',
                'Tip combustibil',
                'Total vehicule',
            ];

            $rows = [];

            foreach ($result as $row) {
                $rows[] = [
                    $this->getArrayValue($row, 'id', ''),
                    $this->getArrayValue($row, 'name', ''),
                    $this->getArrayValue($row, 'total_vehicles', 0),
                ];
            }

            return [
                'filename' => $this->buildFileName('statistics_fuel_distribution'),
                'content_type' => 'text/csv; charset=utf-8',
                'content' => $this->convertRowsToCsv($headers, $rows),
            ];
        }

        if ($view === 'county-ranking') { ///apeleaza clasamentul judetelor
            $result = $this->statisticsRepository->getCountyRanking($filters);

            $headers = [
                'ID județ',
                'Cod județ',
                'Județ',
                'Total vehicule',
            ];

            $rows = [];

            foreach ($result as $row) {
                $rows[] = [
                    $this->getArrayValue($row, 'id', ''),
                    $this->getArrayValue($row, 'code', ''),
                    $this->getArrayValue($row, 'name', ''),
                    $this->getArrayValue($row, 'total_vehicles', 0),
                ];
            }

            return [
                'filename' => $this->buildFileName('statistics_county_ranking'),
                'content_type' => 'text/csv; charset=utf-8',
                'content' => $this->convertRowsToCsv($headers, $rows),
            ];
        }

        if ($view === 'category-distribution') { ///distributia pe categorii nationale
            $result = $this->statisticsRepository->getNationalCategoryDistribution($filters);

            $headers = [
                'ID categorie',
                'Categorie națională',
                'Total vehicule',
            ];

            $rows = [];

            foreach ($result as $row) {
                $rows[] = [
                    $this->getArrayValue($row, 'id', ''),
                    $this->getArrayValue($row, 'name', ''),
                    $this->getArrayValue($row, 'total_vehicles', 0),
                ];
            }

            return [
                'filename' => $this->buildFileName('statistics_category_distribution'),
                'content_type' => 'text/csv; charset=utf-8',
                'content' => $this->convertRowsToCsv($headers, $rows),
            ];
        }

        throw new InvalidArgumentException('View-ul statistic cerut pentru export nu este suportat.');
    }

    private function buildMapExport(array $params): array ///exportul pentru componenta cartografica
    {
        if (!isset($params['year']) || $params['year'] === null || $params['year'] === '') {
            throw new InvalidArgumentException('Parametrul year este obligatoriu pentru exportul hărții.');
        }

        $year = (int) $params['year']; ///extrage filtrele relevante pentru harta
        $fuelType = $this->getArrayValue($params, 'fuel_type', null);
        $nationalCategory = $this->getArrayValue($params, 'national_category', null);

        $rows = $this->mapRepository->getCountyTopBrandsByYear($year, $fuelType, $nationalCategory);

        $headers = [
            'Cod județ',
            'Județ',
            'Marcă predominantă',
            'Total vehicule',
        ];

        $csvRows = [];

        foreach ($rows as $row) {
            $csvRows[] = [
                $this->getArrayValue($row, 'county_code', ''),
                $this->getArrayValue($row, 'county_name', ''),
                $this->getArrayValue($row, 'top_brand', ''),
                $this->getArrayValue($row, 'total_vehicles', 0),
            ];
        }

        return [
            'filename' => $this->buildFileName('map_export'),
            'content_type' => 'text/csv; charset=utf-8',
            'content' => $this->convertRowsToCsv($headers, $csvRows),
        ];
    }

    private function buildFileName(string $prefix): string 
    ///construieste numele fisierului de export
    {
        return $prefix . '_' . date('Y-m-d_H-i-s') . '.csv';
    }

    private function convertRowsToCsv(array $headers, array $rows): string 
    /*Primește headerele și rândurile de date și 
 generează conținutul final CSV. */
    {
        $stream = fopen('php://temp', 'r+'); ///Creează un flux temporar în memorie.

        if ($stream === false) {
            throw new RuntimeException('Nu s-a putut crea fluxul temporar pentru export.');
        }

        fwrite($stream, "\xEF\xBB\xBF"); ///Scrie UTF-8 la începutul fișierului.

        fputcsv($stream, $headers);

        foreach ($rows as $row) {
            fputcsv($stream, $row);
        }

        rewind($stream);
        $content = stream_get_contents($stream);
        fclose($stream);

        if ($content === false) {
            throw new RuntimeException('Nu s-a putut genera conținutul CSV.');
        }

        return $content;
    }

    private function getArrayValue(array $array, string $key, $default)

///citește o valoare dintr-un array și întoarce un fallback dacă cheia nu există.
    {
        if (isset($array[$key])) {
            return $array[$key];
        }

        return $default;
    }
}