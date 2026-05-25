<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Database.php';

class FilterRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
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
            static fn(array $row): int => (int)$row['year'],
            $rows
        );
    }

    public function getAvailableCounties(): array
    {
        $sql = '
            SELECT DISTINCT
                c.code,
                c.name
            FROM vehicle_records vr
            INNER JOIN counties c ON vr.county_id = c.id
            ORDER BY c.name ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getAvailableNationalCategories(): array
    {
        $sql = '
            SELECT DISTINCT
                vc.id,
                vc.name
            FROM vehicle_records vr
            INNER JOIN vehicle_categories vc ON vr.national_category_id = vc.id
            ORDER BY vc.name ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getAvailableCommunityCategories(): array
    {
        $sql = '
            SELECT DISTINCT
                cc.id,
                cc.name
            FROM vehicle_records vr
            INNER JOIN community_categories cc ON vr.community_category_id = cc.id
            ORDER BY cc.name ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getAvailableFuelTypes(): array
    {
        $sql = '
            SELECT DISTINCT
                ft.id,
                ft.name
            FROM vehicle_records vr
            INNER JOIN fuel_types ft ON vr.fuel_type_id = ft.id
            ORDER BY ft.name ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getAvailableBrands(): array
    {
        $sql = '
            SELECT DISTINCT
                b.id,
                b.name
            FROM vehicle_records vr
            INNER JOIN brands b ON vr.brand_id = b.id
            ORDER BY b.name ASC
        ';

        $stmt = $this->pdo->query($sql);

        return $stmt->fetchAll();
    }

    public function getAllFilters(): array
    {
        return [
            'years' => $this->getAvailableYears(),
            'counties' => $this->getAvailableCounties(),
            'national_categories' => $this->getAvailableNationalCategories(),
            'community_categories' => $this->getAvailableCommunityCategories(),
            'fuel_types' => $this->getAvailableFuelTypes(),
            'brands' => $this->getAvailableBrands(),
        ];
    }
}