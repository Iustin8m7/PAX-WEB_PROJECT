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

    private function buildFilterSql(array $filters, array &$params): string
    {
        $sql = '';

        if (!empty($filters['year'])) {
            $sql .= ' AND vr.year = :year ';
            $params[':year'] = (int) $filters['year'];
        }

        if (!empty($filters['county_code'])) {
            $sql .= ' AND c.code = :county_code ';
            $params[':county_code'] = $filters['county_code'];
        }

        if (!empty($filters['national_category'])) {
            $sql .= ' AND vc.name = :national_category ';
            $params[':national_category'] = $filters['national_category'];
        }

        if (!empty($filters['community_category'])) {
            $sql .= ' AND cc.name = :community_category ';
            $params[':community_category'] = $filters['community_category'];
        }

        if (!empty($filters['fuel_type'])) {
            $sql .= ' AND ft.name = :fuel_type ';
            $params[':fuel_type'] = $filters['fuel_type'];
        }

        if (!empty($filters['brand'])) {
            $sql .= ' AND b.name = :brand ';
            $params[':brand'] = $filters['brand'];
        }

        return $sql;
    }

    private function bindParams(PDOStatement $stmt, array $params): void
    {
        foreach ($params as $key => $value) {
            if ($key === ':year' || $key === ':limit') {
                $stmt->bindValue($key, (int) $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, (string) $value, PDO::PARAM_STR);
            }
        }
    }

    public function getYearlyTotals(array $filters = []): array
    {
        $sql = '
            SELECT
                vr.year,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            WHERE 1 = 1
        ';

        $params = [];
        $filtersWithoutYear = $filters;
        unset($filtersWithoutYear['year']);

        $sql .= $this->buildFilterSql($filtersWithoutYear, $params);

        $sql .= '
            GROUP BY vr.year
            ORDER BY vr.year ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getTopBrands(array $filters = [], int $limit = 10): array
    {
        $sql = '
            SELECT
                b.id,
                b.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            INNER JOIN brands b ON vr.brand_id = b.id
            WHERE 1 = 1
        ';

        $params = [];
        $sql .= $this->buildFilterSql($filters, $params);

        $sql .= '
            GROUP BY b.id, b.name
            ORDER BY total_vehicles DESC, b.name ASC
            LIMIT :limit
        ';

        $params[':limit'] = $limit;

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getFuelDistribution(array $filters = []): array
    {
        $sql = '
            SELECT
                ft.id,
                ft.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            WHERE 1 = 1
        ';

        $params = [];
        $sql .= $this->buildFilterSql($filters, $params);

        $sql .= '
            GROUP BY ft.id, ft.name
            ORDER BY total_vehicles DESC, ft.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getCountyRanking(array $filters = []): array
    {
        $sql = '
            SELECT
                c.id,
                c.code,
                c.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            WHERE 1 = 1
        ';

        $params = [];
        $sql .= $this->buildFilterSql($filters, $params);

        $sql .= '
            GROUP BY c.id, c.code, c.name
            ORDER BY total_vehicles DESC, c.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getNationalCategoryDistribution(array $filters = []): array
    {
        $sql = '
            SELECT
                vc.id,
                vc.name,
                SUM(vr.vehicle_count) AS total_vehicles
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            WHERE 1 = 1
        ';

        $params = [];
        $sql .= $this->buildFilterSql($filters, $params);

        $sql .= '
            GROUP BY vc.id, vc.name
            ORDER BY total_vehicles DESC, vc.name ASC
        ';

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getOverview(array $filters = []): array
    {
        $sql = '
            SELECT
                SUM(vr.vehicle_count) AS total_vehicles,
                COUNT(DISTINCT vr.county_id) AS counties_count,
                COUNT(DISTINCT vr.brand_id) AS brands_count,
                COUNT(DISTINCT vr.fuel_type_id) AS fuel_types_count,
                COUNT(DISTINCT vr.national_category_id) AS national_categories_count
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            WHERE 1 = 1
        ';

        $params = [];
        $sql .= $this->buildFilterSql($filters, $params);

        $stmt = $this->pdo->prepare($sql);
        $this->bindParams($stmt, $params);
        $stmt->execute();

        $row = $stmt->fetch();

        if (isset($filters['year'])) {
            $year = $filters['year'];
        } else {
            $year = null;
        }

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
            $totalVehicles = (int) $row['total_vehicles'];
        } else {
            $totalVehicles = 0;
        }

        if (isset($row['counties_count'])) {
            $countiesCount = (int) $row['counties_count'];
        } else {
            $countiesCount = 0;
        }

        if (isset($row['brands_count'])) {
            $brandsCount = (int) $row['brands_count'];
        } else {
            $brandsCount = 0;
        }

        if (isset($row['fuel_types_count'])) {
            $fuelTypesCount = (int) $row['fuel_types_count'];
        } else {
            $fuelTypesCount = 0;
        }

        if (isset($row['national_categories_count'])) {
            $nationalCategoriesCount = (int) $row['national_categories_count'];
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