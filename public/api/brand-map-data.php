<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/core/Response.php';
require_once __DIR__ . '/../../app/helpers/validators.php';
require_once __DIR__ . '/../../app/helpers/normalizers.php';
require_once __DIR__ . '/../../app/repositories/MapRepository.php';

try {
    $year = getYearParam('year', true);
    $fuelType = getOptionalStringParam('fuel_type');
    $nationalCategory = getOptionalStringParam('national_category');

    $repository = new MapRepository();
    $rows = $repository->getCountyTopBrandsByYear($year, $fuelType, $nationalCategory);
    $rows = normalizeUtf8Value($rows);

    Response::success([
        'year' => $year,
        'filters' => [
            'fuel_type' => $fuelType,
            'national_category' => $nationalCategory,
        ],
        'result' => $rows,
    ]);
} catch (Throwable $e) {
    Response::error('Nu s-au putut încărca datele pentru harta brandurilor.', 500, [
        'exception' => $e->getMessage(),
    ]);
}
