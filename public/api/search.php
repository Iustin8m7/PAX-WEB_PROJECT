<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/core/Response.php';
require_once __DIR__ . '/../../app/helpers/validators.php';
require_once __DIR__ . '/../../app/helpers/normalizers.php';
require_once __DIR__ . '/../../app/repositories/VehicleRepository.php';

try {
    $repository = new VehicleRepository();

    $filters = [
        'year' => getYearParam('year', false),
        'county_code' => getOptionalStringParam('county_code'),
        'national_category' => getOptionalStringParam('national_category'),
        'community_category' => getOptionalStringParam('community_category'),
        'brand' => getOptionalStringParam('brand'),
        'fuel_type' => getOptionalStringParam('fuel_type'),
        'model' => getOptionalStringParam('model'),
    ];

    $page = getPageParam('page');
    $limit = getLimitParam('limit');

    $sort = getSortFieldParam(
        ['year', 'county', 'national_category', 'community_category', 'brand', 'fuel_type', 'vehicle_count'],
        'vehicle_count',
        'sort'
    );

    $order = getSortOrderParam('order', 'desc');

    $total = $repository->countSearchResults($filters);
    $results = $repository->search($filters, $page, $limit, $sort, $order);

    $filters = normalizeUtf8Value($filters);
    $results = normalizeUtf8Value($results);

    $totalPages = $limit > 0 ? (int)ceil($total / $limit) : 1;

    Response::success([
        'filters' => $filters,
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total_results' => $total,
            'total_pages' => $totalPages,
        ],
        'sorting' => [
            'sort' => $sort,
            'order' => $order,
        ],
        'result' => $results,
    ]);
} catch (Throwable $e) {
    Response::error('Nu s-au putut incarca rezultatele cautarii.', 500, [
        'exception' => $e->getMessage(),
    ]);
}