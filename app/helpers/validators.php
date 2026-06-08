<?php

declare(strict_types=1);

require_once __DIR__ . '/../core/Response.php';

function getConfig(): array
{
    return require __DIR__ . '/../config/config.php';
}

function getQueryParam(string $key, mixed $default = null): mixed
{
    if (isset($_GET[$key])) {
        return $_GET[$key];
    }

    return $default;
}

function getOptionalStringParam(string $key)
{
    $value = getQueryParam($key);

    if ($value === null) {
        return null;
    }

    $value = trim((string) $value);

    if ($value === '') {
        return null;
    }

    return $value;
}

function getRequiredStringParam(string $key): string
{
    $value = getOptionalStringParam($key);

    if ($value === null) {
        Response::error("Parametrul '{$key}' este obligatoriu.", 400);
    }

    return $value;
}

function getIntParam(string $key, $default = null)
{
    $value = getQueryParam($key);

    if ($value === null || $value === '') {
        return $default;
    }

    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
        Response::error("Parametrul '{$key}' trebuie să fie un număr întreg valid.", 400);
    }

    return (int) $value;
}

function validateYear($year, bool $required = false)
{
    $config = getConfig();
    $minYear = (int) $config['app']['min_year'];
    $maxYear = (int) $config['app']['max_year'];

    if ($year === null) {
        if ($required) {
            Response::error('Parametrul year este obligatoriu.', 400);
        }

        return null;
    }

    if ($year < $minYear || $year > $maxYear) {
        Response::error("Parametrul year trebuie să fie între {$minYear} și {$maxYear}.", 400);
    }

    return $year;
}

function getYearParam(string $key = 'year', bool $required = false)
{
    $year = getIntParam($key);
    return validateYear($year, $required);
}

function validateLimit($limit): int
{
    $config = getConfig();
    $defaultPageSize = (int) $config['app']['default_page_size'];
    $maxPageSize = (int) $config['app']['max_page_size'];

    if ($limit === null) {
        return $defaultPageSize;
    }

    if ($limit < 1 || $limit > $maxPageSize) {
        Response::error("Parametrul limit trebuie să fie între 1 și {$maxPageSize}.", 400);
    }

    return $limit;
}

function getLimitParam(string $key = 'limit'): int
{
    $limit = getIntParam($key);
    return validateLimit($limit);
}

function validatePage($page): int
{
    if ($page === null) {
        return 1;
    }

    if ($page < 1) {
        Response::error('Parametrul page trebuie să fie mai mare sau egal cu 1.', 400);
    }

    return $page;
}

function getPageParam(string $key = 'page'): int
{
    $page = getIntParam($key);
    return validatePage($page);
}

function validateSortField($sort, array $allowedFields, string $default, string $paramName = 'sort'): string
{
    if ($sort === null) {
        return $default;
    }

    if (!in_array($sort, $allowedFields, true)) {
        Response::error("Parametrul '{$paramName}' nu are o valoare permisă.", 400, [
            'allowed' => $allowedFields,
        ]);
    }

    return $sort;
}

function getSortFieldParam(array $allowedFields, string $default, string $key = 'sort'): string
{
    $sort = getOptionalStringParam($key);
    return validateSortField($sort, $allowedFields, $default, $key);
}

function validateSortOrder($order, string $default = 'asc', string $paramName = 'order'): string
{
    if ($order === null) {
        return $default;
    }

    $normalized = strtolower(trim($order));

    if (!in_array($normalized, ['asc', 'desc'], true)) {
        Response::error("Parametrul '{$paramName}' trebuie să fie 'asc' sau 'desc'.", 400);
    }

    return $normalized;
}

function getSortOrderParam(string $key = 'order', string $default = 'asc'): string
{
    $order = getOptionalStringParam($key);
    return validateSortOrder($order, $default, $key);
}

function validateAllowedValue($value, array $allowedValues, string $paramName)
{
    if ($value === null) {
        return null;
    }

    if (!in_array($value, $allowedValues, true)) {
        Response::error("Parametrul '{$paramName}' nu are o valoare permisă.", 400, [
            'allowed' => $allowedValues,
        ]);
    }

    return $value;
}