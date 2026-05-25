<?php

declare(strict_types=1);

require_once __DIR__ . '/app/repositories/FilterRepository.php';

$repository = new FilterRepository();
$filters = $repository->getAllFilters();

$json = json_encode($filters, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

var_dump($json);
var_dump(json_last_error_msg());
