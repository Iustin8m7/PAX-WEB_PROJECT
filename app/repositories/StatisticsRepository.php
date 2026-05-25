<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Database.php';

class StatisticsRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function getYearlyTotals(): array
    {
        $sql = '
            SELECT
                year,
                SUM(vehicle_count) AS total_vehicles
            FROM vehicle_records
            GROUP BY year
            ORDER BY year ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getTopBrandsByYear(int $year, int $limit = 10): array
    {
        $sql = '
            SELECT
                b.id,
                b.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN brands b ON vr.brand_id = b.id
            WHERE vr.year = :year
            GROUP BY b.id, b.name
            ORDER BY total_vehicles DESC, b.name ASC
            LIMIT :limit
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':year', $year, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getFuelDistributionByYear(int $year): array
    {
        $sql = '
            SELECT
                ft.id,
                ft.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            WHERE vr.year = :year
            GROUP BY ft.id, ft.name
            ORDER BY total_vehicles DESC, ft.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
        ]);

        return $stmt->fetchAll();
    }

    public function getCountyRankingByYear(int $year): array
    {
        $sql = '
            SELECT
                c.id,
                c.code,
                c.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            WHERE vr.year = :year
            GROUP BY c.id, c.code, c.name
            ORDER BY total_vehicles DESC, c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
        ]);

        return $stmt->fetchAll();
    }

    public function getNationalCategoryDistributionByYear(int $year): array
    {
        $sql = '
            SELECT
                vc.id,
                vc.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            WHERE vr.year = :year
            GROUP BY vc.id, vc.name
            ORDER BY total_vehicles DESC, vc.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
        ]);

        return $stmt->fetchAll();
    }

    public function getOverviewByYear(int $year): array
    {
        $sql = '
            SELECT
                SUM(vr.vehicle_count) AS total_vehicles,
                COUNT(DISTINCT vr.county_id) AS counties_count,
                COUNT(DISTINCT vr.brand_id) AS brands_count,
                COUNT(DISTINCT vr.fuel_type_id) AS fuel_types_count,
                COUNT(DISTINCT vr.national_category_id) AS national_categories_count
            FROM vehicle_records vr
            WHERE vr.year = :year
        ';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            ':year' => $year,
        ]);

        $row = $stmt->fetch();

        if ($row === false) {
            return [
                'year' => $year,
                'total_vehicles' => 0,
                'counties_count' => 0,
                'brands_count' => 0,
                'fuel_types_count' => 0,
                'national_categories_count' => 0,
            ];
        }

        if (isset($row['total_vehicles'])) {
            $totalVehicles = (int)$row['total_vehicles'];
        } else {
            $totalVehicles = 0;
        }

        if (isset($row['counties_count'])) {
            $countiesCount = (int)$row['counties_count'];
        } else {
            $countiesCount = 0;
        }

        if (isset($row['brands_count'])) {
            $brandsCount = (int)$row['brands_count'];
        } else {
            $brandsCount = 0;
        }

        if (isset($row['fuel_types_count'])) {
            $fuelTypesCount = (int)$row['fuel_types_count'];
        } else {
            $fuelTypesCount = 0;
        }

        if (isset($row['national_categories_count'])) {
            $nationalCategoriesCount = (int)$row['national_categories_count'];
        } else {
            $nationalCategoriesCount = 0;
        }

        return [
            'year' => $year,
            'total_vehicles' => $totalVehicles,
            'counties_count' => $countiesCount,
            'brands_count' => $brandsCount,
            'fuel_types_count' => $fuelTypesCount,
            'national_categories_count' => $nationalCategoriesCount,
        ];
    }
}