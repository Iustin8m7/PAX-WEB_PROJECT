<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/core/Response.php';
require_once __DIR__ . '/../../app/helpers/validators.php';
require_once __DIR__ . '/../../app/helpers/normalizers.php';
require_once __DIR__ . '/../../app/repositories/MapRepository.php';

try {
    $repository = new MapRepository();

    $year = getYearParam('year', true);
    $fuelType = getOptionalStringParam('fuel_type');
    $nationalCategory = getOptionalStringParam('national_category');

    $result = $repository->getCountyTopBrandsByYear($year, $fuelType, $nationalCategory);
    $result = normalizeUtf8Value($result);

    Response::success([
        'year' => $year,
        'filters' => [
            'fuel_type' => $fuelType,
            'national_category' => $nationalCategory,
        ],
        'result' => $result,
    ]);
} catch (Throwable $e) {
    Response::error('Nu s-au putut încărca datele pentru hartă.', 500);
}