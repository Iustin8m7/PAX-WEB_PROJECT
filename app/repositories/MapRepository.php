<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Database.php';

class MapRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getCountyTotalsByYear(int $year): array
    {
        $sql = '
            SELECT
                c.code AS county_code,
                c.name AS county_name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            WHERE vr.year = :year
            GROUP BY c.code, c.name
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
        ]);

        return $stmt->fetchAll();
    }

    public function getCountyTotalsByYearAndFuelType(int $year, string $fuelType): array
    {
        $sql = '
            SELECT
                c.code AS county_code,
                c.name AS county_name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            WHERE vr.year = :year
              AND ft.name = :fuel_type
            GROUP BY c.code, c.name
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
            ':fuel_type' => $fuelType,
        ]);

        return $stmt->fetchAll();
    }

    public function getCountyTotalsByYearAndNationalCategory(int $year, string $nationalCategory): array
    {
        $sql = '
            SELECT
                c.code AS county_code,
                c.name AS county_name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            WHERE vr.year = :year
              AND vc.name = :national_category
            GROUP BY c.code, c.name
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
            ':national_category' => $nationalCategory,
        ]);

        return $stmt->fetchAll();
    }

    public function getCountyTotalsFiltered(
        int $year,
        ?string $fuelType = null,
        ?string $nationalCategory = null
    ): array {
        $sql = '
            SELECT
                c.code AS county_code,
                c.name AS county_name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            LEFT JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            WHERE vr.year = :year
        ';

        $params = [
            ':year' => $year,
        ];

        if ($fuelType !== null) {
            $sql .= ' AND ft.name = :fuel_type ';
            $params[':fuel_type'] = $fuelType;
        }

        if ($nationalCategory !== null) {
            $sql .= ' AND vc.name = :national_category ';
            $params[':national_category'] = $nationalCategory;
        }

        $sql .= '
            GROUP BY c.code, c.name
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public function getCountyTopBrandsByYear(
        int $year,
        ?string $fuelType = null,
        ?string $nationalCategory = null
    ): array {
        $sql = '
            SELECT
                c.code AS county_code,
                c.name AS county_name,
                b.name AS brand_name,
                SUM(vr.vehicle_count) AS brand_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            LEFT JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            WHERE vr.year = :year
        ';

        $params = [
            ':year' => $year,
        ];

        if ($fuelType !== null) {
            $sql .= ' AND ft.name = :fuel_type ';
            $params[':fuel_type'] = $fuelType;
        }

        if ($nationalCategory !== null) {
            $sql .= ' AND vc.name = :national_category ';
            $params[':national_category'] = $nationalCategory;
        }

        $sql .= '
            GROUP BY c.code, c.name, b.name
            ORDER BY c.name ASC, brand_vehicles DESC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $rows = $stmt->fetchAll();

        $countyTotals = [];
        $topBrands = [];

        foreach ($rows as $row) {
            $countyCode = $row['county_code'];
            $brandVehicles = isset($row['brand_vehicles']) ? (int) $row['brand_vehicles'] : 0;

            if (!isset($countyTotals[$countyCode])) {
                $countyTotals[$countyCode] = 0;
            }

            $countyTotals[$countyCode] += $brandVehicles;

            $currentTop = $topBrands[$countyCode] ?? null;

            if ($currentTop === null || $brandVehicles > $currentTop['brand_vehicles']) {
                $topBrands[$countyCode] = [
                    'county_code' => $countyCode,
                    'county_name' => $row['county_name'],
                    'top_brand' => $row['brand_name'] ?? 'Necunoscut',
                    'brand_vehicles' => $brandVehicles,
                ];
            }
        }

        $result = [];

        foreach ($topBrands as $countyCode => $data) {
            $result[] = [
                'county_code' => $data['county_code'],
                'county_name' => $data['county_name'],
                'top_brand' => $data['top_brand'],
                'total_vehicles' => $countyTotals[$countyCode] ?? 0,
            ];
        }

        usort($result, static function (array $a, array $b): int {
            return strcmp($a['county_name'], $b['county_name']);
        });

        return $result;
    }
}