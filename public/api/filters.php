<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/core/Response.php';
require_once __DIR__ . '/../../app/helpers/normalizers.php';
require_once __DIR__ . '/../../app/repositories/FilterRepository.php';

try {
    $repository = new FilterRepository();
    $filters = $repository->getAllFilters();
    $filters = normalizeUtf8Value($filters);

    Response::success($filters);
} catch (Throwable $e) {
    Response::error('Nu s-au putut încărca filtrele.', 500);
}