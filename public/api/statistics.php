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

    if ($view === 'overview') {
        $year = getYearParam('year', true);
        $data = $repository->getOverviewByYear($year);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'year' => $year,
            'result' => $data,
        ]);
    } else if ($view === 'yearly-totals') {
        $data = $repository->getYearlyTotals();
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'result' => $data,
        ]);
    } else if ($view === 'top-brands') {
        $year = getYearParam('year', true);
        $limit = getLimitParam('limit');
        $data = $repository->getTopBrandsByYear($year, $limit);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'year' => $year,
            'limit' => $limit,
            'result' => $data,
        ]);
    } else if ($view === 'fuel-distribution') {
        $year = getYearParam('year', true);
        $data = $repository->getFuelDistributionByYear($year);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'year' => $year,
            'result' => $data,
        ]);
    } else if ($view === 'county-ranking') {
        $year = getYearParam('year', true);
        $data = $repository->getCountyRankingByYear($year);
        $data = normalizeUtf8Value($data);

        Response::success([
            'view' => $view,
            'year' => $year,
            'result' => $data,
        ]);
    } else if ($view === 'category-distribution') {
        $year = getYearParam('year', true);
        $data = $repository->getNationalCategoryDistributionByYear($year);
        $data = normalizeUtf8Value($data);
        
        Response::success([
            'view' => $view,
            'year' => $year,
            'result' => $data,
        ]);
    } else {
        Response::error('Valoarea parametrului view nu este suportata.', 400);
    }
} catch (Throwable $e) {
    Response::error('Nu s-au putut incarca statisticile.', 500, [
        'exception' => $e->getMessage(),
    ]);
}