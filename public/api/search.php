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
        [
            'year',
            'county_code',
            'county_name',
            'national_category',
            'community_category',
            'brand_name',
            'model_description',
            'fuel_type',
            'vehicle_count',
        ],
        'vehicle_count',
        'sort_by'
    );

    $order = getSortOrderParam('sort_order', 'desc');

    $total = $repository->countSearchResults($filters);
    $results = $repository->search($filters, $page, $limit, $sort, $order);

    $filters = normalizeUtf8Value($filters);
    $results = normalizeUtf8Value($results);

    $totalPages = $limit > 0 ? (int) ceil($total / $limit) : 1;

    Response::success([
        'filters' => $filters,
        'rows' => $results,
        'total' => $total,
        'page' => $page,
        'pages' => $totalPages,
        'limit' => $limit,
        'sort_by' => $sort,
        'sort_order' => $order,
    ]);
} catch (Throwable $e) {
    Response::error('Nu s-au putut încărca rezultatele căutării.', 500);
}