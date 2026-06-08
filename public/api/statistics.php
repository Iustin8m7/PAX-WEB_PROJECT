<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/normalizers.php';
require_once __DIR__ . '/../../app/core/Response.php';
require_once __DIR__ . '/../../app/helpers/validators.php';
require_once __DIR__ . '/../../app/repositories/StatisticsRepository.php';

try {
    $repository = new StatisticsRepository();

    $allowedViews = [
        'overview',
        'yearly-totals',
        'top-brands',
        'fuel-distribution',
        'county-ranking',
        'category-distribution',
    ];

    $view = getOptionalStringParam('view');
    $view = validateAllowedValue($view, $allowedViews, 'view');

    if ($view === null) {
        $view = 'overview';
    }

    $filters = [
        'year' => getYearParam('year', false),
        'county_code' => getOptionalStringParam('county_code'),
        'national_category' => getOptionalStringParam('national_category'),
        'community_category' => getOptionalStringParam('community_category'),
        'fuel_type' => getOptionalStringParam('fuel_type'),
        'brand' => getOptionalStringParam('brand'),
    ];

    if ($view === 'overview') {
        if ($filters['year'] === null) {
            Response::error('Parametrul year este obligatoriu pentru view=overview.', 400);
        }

        $data = $repository->getOverview($filters);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'filters' => $filters,
            'result' => $data,
        ]);
    } elseif ($view === 'yearly-totals') {
        $data = $repository->getYearlyTotals($filters);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'filters' => $filters,
            'result' => $data,
        ]);
    } elseif ($view === 'top-brands') {
        if ($filters['year'] === null) {
            Response::error('Parametrul year este obligatoriu pentru view=top-brands.', 400);
        }

        $limit = getLimitParam('limit');
        $data = $repository->getTopBrands($filters, $limit);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'filters' => $filters,
            'limit' => $limit,
            'result' => $data,
        ]);
    } elseif ($view === 'fuel-distribution') {
        if ($filters['year'] === null) {
            Response::error('Parametrul year este obligatoriu pentru view=fuel-distribution.', 400);
        }

        $data = $repository->getFuelDistribution($filters);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'filters' => $filters,
            'result' => $data,
        ]);
    } elseif ($view === 'county-ranking') {
        if ($filters['year'] === null) {
            Response::error('Parametrul year este obligatoriu pentru view=county-ranking.', 400);
        }

        $data = $repository->getCountyRanking($filters);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'filters' => $filters,
            'result' => $data,
        ]);
    } elseif ($view === 'category-distribution') {
        if ($filters['year'] === null) {
            Response::error('Parametrul year este obligatoriu pentru view=category-distribution.', 400);
        }

        $data = $repository->getNationalCategoryDistribution($filters);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'filters' => $filters,
            'result' => $data,
        ]);
    } else {
        Response::error('Valoarea parametrului view nu este suportată.', 400);
    }
} catch (Throwable $e) {
    Response::error('Nu s-au putut încărca statisticile.', 500);
}