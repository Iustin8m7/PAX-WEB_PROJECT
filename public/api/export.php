<?php

declare(strict_types=1);

require_once __DIR__ . '/../../app/helpers/validators.php';
require_once __DIR__ . '/../../app/services/ExportService.php';

try {
    $resource = getRequiredStringParam('resource');
    $format = getOptionalStringParam('format');

    if ($format === null) {
        $format = 'csv';
    }

    $params = [
        'view' => getOptionalStringParam('view'),
        'year' => getYearParam('year', false),
        'county_code' => getOptionalStringParam('county_code'),
        'national_category' => getOptionalStringParam('national_category'),
        'community_category' => getOptionalStringParam('community_category'),
        'fuel_type' => getOptionalStringParam('fuel_type'),
        'brand' => getOptionalStringParam('brand'),
        'model' => getOptionalStringParam('model'),
        'sort_by' => getOptionalStringParam('sort_by'),
        'sort_order' => getOptionalStringParam('sort_order'),
        'limit' => getIntParam('limit'),
    ];

    $service = new ExportService();
    $payload = $service->buildExportPayload($resource, $format, $params);

    if (!isset($payload['filename'], $payload['content_type'], $payload['content'])) {
        throw new RuntimeException('Payload-ul de export este invalid.');
    }

    $filename = (string) $payload['filename'];
    $contentType = (string) $payload['content_type'];
    $content = (string) $payload['content'];

    header('Content-Type: ' . $contentType);
    header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
    ///Spune browserului că răspunsul trebuie tratat ca atașament descărcabil, nu doar afișat în pagină.
    header('Content-Length: ' . (string) strlen($content));
    ///Trimite lungimea conținutului în bytes.
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    ///Spun browserului și eventualelor proxy-uri să nu cache-uiască agresiv răspunsul.
    header('Pragma: no-cache');

    echo $content;
    exit;
} catch (Throwable $e) {
    require_once __DIR__ . '/../../app/core/Response.php';

    Response::error('Nu s-a putut genera exportul solicitat.', 500);
    ///Trimite un răspuns JSON standardizat de eroare cu status code 500.
}