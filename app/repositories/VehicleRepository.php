<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Database.php';

class VehicleRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function search(
        array $filters,
        int $page = 1,
        int $limit = 25,
        string $sort = 'vehicle_count',
        string $order = 'desc'
    ): array {
        $allowedSortFields = [
            'year' => 'vr.year',
            'county_code' => 'c.code',
            'county_name' => 'c.name',
            'national_category' => 'vc.name',
            'community_category' => 'cc.name',
            'brand_name' => 'b.name',
            'model_description' => 'vr.model_description',
            'fuel_type' => 'ft.name',
            'vehicle_count' => 'vr.vehicle_count',
        ];

        if (isset($allowedSortFields[$sort])) {
            $sortColumn = $allowedSortFields[$sort];
        } else {
            $sortColumn = 'vr.vehicle_count';
        }

        if (strtolower($order) === 'asc') {
            $sortDirection = 'ASC';
        } else {
            $sortDirection = 'DESC';
        }

        $offset = ($page - 1) * $limit;

        $sql = '
            SELECT
                vr.id,
                vr.year,
                c.code AS county_code,
                c.name AS county_name,
                vc.name AS national_category,
                cc.name AS community_category,
                b.name AS brand_name,
                vr.model_description,
                ft.name AS fuel_type,
                vr.vehicle_count
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            WHERE 1 = 1
        ';

        $params = [];

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

        if (!empty($filters['brand'])) {
            $sql .= ' AND b.name = :brand ';
            $params[':brand'] = $filters['brand'];
        }

        if (!empty($filters['fuel_type'])) {
            $sql .= ' AND ft.name = :fuel_type ';
            $params[':fuel_type'] = $filters['fuel_type'];
        }

        if (!empty($filters['model'])) {
            $sql .= ' AND vr.model_description LIKE :model ';
            $params[':model'] = '%' . $filters['model'] . '%';
        }

        $sql .= " ORDER BY {$sortColumn} {$sortDirection}, vr.id ASC ";
        $sql .= ' LIMIT :limit OFFSET :offset ';

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === ':year') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function countSearchResults(array $filters): int
    {
        $sql = '
            SELECT COUNT(*) AS total
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            LEFT JOIN community_categories cc ON vr.community_category_id = cc.id
            LEFT JOIN brands b ON vr.brand_id = b.id
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            WHERE 1 = 1
        ';

        $params = [];

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

        if (!empty($filters['brand'])) {
            $sql .= ' AND b.name = :brand ';
            $params[':brand'] = $filters['brand'];
        }

        if (!empty($filters['fuel_type'])) {
            $sql .= ' AND ft.name = :fuel_type ';
            $params[':fuel_type'] = $filters['fuel_type'];
        }

        if (!empty($filters['model'])) {
            $sql .= ' AND vr.model_description LIKE :model ';
            $params[':model'] = '%' . $filters['model'] . '%';
        }

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            if ($key === ':year') {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            }
        }

        $stmt->execute();

        $row = $stmt->fetch();

        if (isset($row['total'])) {
            return (int) $row['total'];
        }

        return 0;
    }
}